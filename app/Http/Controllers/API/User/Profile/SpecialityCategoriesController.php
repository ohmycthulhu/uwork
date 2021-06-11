<?php

namespace App\Http\Controllers\API\User\Profile;

use App\Facades\MediaFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\UpdateImageRequest;
use App\Http\Requests\Common\UploadImageRequest;
use App\Http\Requests\Profile\CreateMultipleSpecialityFormRequest;
use App\Http\Requests\Profile\CreateSpecialityFormRequest;
use App\Http\Requests\Profile\SearchSpecialityCategoriesController;
use App\Http\Requests\Profile\UpdateSpecialityFormRequest;
use App\Models\Categories\Category;
use App\Models\Media\Image;
use App\Models\User\Profile;
use App\Models\User\ProfileSpeciality;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SpecialityCategoriesController extends Controller
{
  protected $category;

  public function __construct(Category $category)
  {
    $this->category = $category;
  }

  /**
   * Method to get profile of current user
   *
   * @return ?Profile
   */
  protected function getProfile(): ?Profile
  {
    $user = Auth::user();
    if (!$user) {
      return null;
    }

    return $user->profile()->first();
  }

  /**
   * Route to get categories for specialities
   *
   * @return JsonResponse
  */
  public function getCategories(): JsonResponse {
    $profile = $this->getProfile();

    $categories = Category::query()->top()->alphabetical()->get();
    $result = Category::addServicesFields($categories, $profile, null, false, false);
    return $this->returnSuccess(compact('result'));
  }

  /**
   * Route to get subcategories by parent id
   *
   * @param int $categoryId
   *
   * @return JsonResponse
  */
  public function getSubcategories(int $categoryId): JsonResponse {
    $profile = $this->getProfile();

    $subcategories = Category::query()
      ->parent($categoryId)
      ->alphabetical()
      ->get();

    $result = Category::addServicesFields($subcategories, $profile, $categoryId, true, true);

    return $this->returnSuccess(compact('result'));
  }

  /**
   * Route to search through the categories
   *
   * @param SearchSpecialityCategoriesController $request
   *
   * @return JsonResponse
  */
  public function searchCategories(SearchSpecialityCategoriesController $request): JsonResponse {
    // Get profile
    $profile = $this->getProfile();

    // Extract parameters
    $keyword = $request->input('keyword');
    $parentId = $request->input('parent_id');
    $size = $request->input('size', 15);

    // Search categories
    $categories = Category::searchByName($keyword, $parentId, $size);

    // Map categories
    $result = Category::addServicesFields($categories, $profile, $parentId, true, false);

    return $this->returnSuccess(compact('result'));
  }
}
