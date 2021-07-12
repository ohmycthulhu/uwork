<?php

namespace App\Http\Controllers\API\User\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\SearchSpecialityCategoriesController;
use App\Models\Categories\Category;
use App\Models\User\Profile;
use App\Modifiers\CategoriesModifier;
use App\Search\Builders\CategoriesSearchBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
    $result = CategoriesModifier::make($categories)
      ->addServices($profile, null, false, false)
      ->execute();
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

    $result = CategoriesModifier::make($subcategories)
      ->addServices($profile, $categoryId, true, true)
      ->execute();

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
    $parentId = $request->input('parent_id');
    $size = $request->input('size', 15);

    // Search categories
    $searchBuilder = new CategoriesSearchBuilder($this->category);
    $searchBuilder->setSize($size);
    if ($keyword = $request->input('keyword')) {
      $searchBuilder->setName($keyword);
    }
    if ($parentId) {
      $searchBuilder->setParentId($parentId);
    }

    $categories = $searchBuilder->execute()->getModels();

    // Map categories
    $result = CategoriesModifier::make($categories)
      ->addServices($profile, $parentId, true, false)
      ->execute();

    return $this->returnSuccess(compact('result'));
  }
}
