<?php

namespace App\Http\Controllers\API\Common;

use App\Facades\SearchFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchCategoriesRequest;
use App\Models\Categories\Category;
use App\Search\Builders\CategoriesSearchBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
  protected $category;

  /**
   * Creates class instance
   *
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
    $searchBuilder = new CategoriesSearchBuilder($this->category);
    $searchBuilder->setSize(10);

    if ($keyword = $request->input('keyword', '')) {
      $searchBuilder->setName($keyword);
    }

    if ($parentCategory = $request->input('parent_id')) {
      $searchBuilder->setParentId($parentCategory);
    }
    $categories = $searchBuilder->execute()->getModels()->load(['parent', 'children']);
    $categories = $this->category::appendBreadcrumbs($categories);

    return $this->returnSuccess(compact('categories'));
  }
}
