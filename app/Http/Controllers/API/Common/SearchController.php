<?php

namespace App\Http\Controllers\API\Common;

use App\Facades\SearchFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ProfileSearchRequest;
use App\Http\Requests\SearchCategoriesRequest;
use App\Models\Categories\Category;
use App\Models\User\Profile;
use App\Models\User\ProfileSpeciality;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
  protected $category;

  /**
   * Creates class instance
   *
   * @param Profile $profile
   * @param Category $category
   */
  public function __construct(Category $category)
  {
    $this->category = $category;
  }

  /**
   * Method to get autocomplete suggestions
   *
   * @param Request $request
   *
   * @return JsonResponse
   */
  public function getAutocomplete(Request $request): JsonResponse {
    $keyword = $request->input('keyword', 'NULL');
    $suggestions = SearchFacade::getAutocomplete($keyword);

    return $this->returnSuccess([
      'suggestions' => $suggestions,
    ]);
  }

  /**
   * Method search category
   *
   * @param SearchCategoriesRequest $request
   *
   * @return JsonResponse
   */
  public function searchCategories(SearchCategoriesRequest $request): JsonResponse {
    $keyword = $request->input('keyword', '');
    $parentCategory = $request->input('parent_id');
    $categories = $this->category::searchByName($keyword, $parentCategory, 10);
    $categories->load(['parent', 'children']);
    $categories = $this->category::appendBreadcrumbs($categories);

    return $this->returnSuccess(compact('categories'));
  }
}
