<?php

namespace App\Http\Controllers\API;

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
  protected $profile;
  protected $speciality;

  /**
   * Creates class instance
   *
   * @param Profile $profile
   * @param Category $category
   * @param ProfileSpeciality $speciality
   */
  public function __construct(Profile $profile, Category $category, ProfileSpeciality $speciality)
  {
    $this->profile = $profile;
    $this->category = $category;
    $this->speciality = $speciality;
  }

  /**
   * Performs search
   *
   * @param ProfileSearchRequest $request
   *
   * @return JsonResponse
   */
  public function search(ProfileSearchRequest $request): JsonResponse {
    $page = $request->input('page', 1);
    // Create query
    $keyword = $request->input('keyword');
    // Get similar categories
    $categories = $request->input('categories', []);
    $categoryId = $request->input('category_id');
    $priceMin = $request->input('price_min');
    $priceMax = $request->input('price_max');

    $sortColumn = $request->input('sort_by', '');
    $sortDir = $request->input('sort_dir', 'asc');

    $specQuery = $this->profile::completeSearch(
      $categoryId,
      $categories,
      $request->input('region_id'),
      $request->input('city_id'),
      $request->input('district_id'),
      $request->input('subway_id'),
      Auth::id(),
      $priceMin,
      $priceMax,
      $sortColumn,
      $sortDir,
      $page,
      $request->input('per_page', 15)
    );

    $profiles = $specQuery->models();
    $profiles->load(['specialities.category', 'user']);

    $profiles = $profiles->map(function (Profile $profile) use ($categoryId, $priceMin, $priceMax) {
      $specialities = $profile->specialities
        ->filter(function (ProfileSpeciality $speciality) use ($categoryId, $priceMin, $priceMax) {
        return $speciality->belongsToCategory($categoryId) &&
          ($priceMin == null || $speciality->price >= $priceMin) &&
          ($priceMax == null || $speciality->price <= $priceMax);
      })->values();
      return array_merge($profile->toArray(), ['specialities' => $specialities]);
    });

    if ($keyword) {
      SearchFacade::registerSearch($keyword);
    }

    // Return response
    return $this->returnSuccess([
      'result' => [
        'data' => $profiles,
        'total' => $specQuery->count(),
        'current_page' => $page,
        'next_page_url' => route('api.profiles.search', array_merge($request->all(), ['page' => $page + 1]))
      ]
    ]);
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
    $categories = Category::appendBreadcrumbs($categories);

    return $this->returnSuccess(compact('categories'));
  }
}
