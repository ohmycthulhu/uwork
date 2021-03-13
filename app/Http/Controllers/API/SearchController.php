<?php

namespace App\Http\Controllers\API;

use App\Facades\SearchFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileSearchRequest;
use App\Models\Categories\Category;
use App\Models\User\Profile;
use App\Models\User\ProfileSpeciality;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
      $categories = $keyword ?
        $this->category::searchByName($keyword)->map(function ($c) { return $c->id; })->toArray() : null;
      $categoryId = $request->input('category_id');

      $specQuery = $this->speciality::completeSearch(
        $categoryId,
        $categories,
        $request->input('region_id'),
        $request->input('city_id'),
        $request->input('district_id'),
        Auth::id(),
        $page,
        100,
      );

      $profileIds = $specQuery->models()->map(function ($s) { return $s->profile_id; })->unique();

      $query = $this->profile::query()
        ->whereIn('id', $profileIds)
        ->with(['specialities.category', 'user']);

      if ($keyword) {
        SearchFacade::registerSearch($keyword);
      }

      // Return response
      return response()->json([
        'result' => [
          'data' => $query->get(),
          'total' => $specQuery->count(),
          'current_page' => $page,
          'next_page_url' => route('api.profiles.search', array_merge($request->all(), ['page' => $page + 1]))
        ]
      ]);
    }

    /**
     * Method to constraint by keyword
     *
     * @param Builder $query
     * @param string $keyword
     *
     * @return Builder
    */
    protected function setKeywordConstraint(Builder $query, string $keyword): Builder {
      $category = $this->category::query()
        ->keyword($keyword)
        ->first();

      if ($category) {
        $query->category($category->id);
      } else {
        $query->whereNull('id'); // Make query fail
      }

      return $query;
    }

    /**
     * Method to constraint by keyword
     *
     * @param Builder $query
     * @param int $categoryId
     *
     * @return Builder
    */
    protected function setCategoryConstraint(Builder $query, int $categoryId): Builder {
      return $query->category($categoryId);
    }

    /**
     * Method to set constraints by region, city and/or district
     *
     * @param Builder $query
     * @param ?int $regionId
     * @param ?int $cityId
     * @param ?int $districtId
     *
     * @return Builder
    */
    protected function setLocationConstraints(Builder $query, ?int $regionId, ?int $cityId, ?int $districtId): Builder {
      if ($regionId) {
        $query->region($regionId);
      }
      if ($cityId) {
        $query->city($cityId);
      }
      if ($districtId) {
        $query->district($districtId);
      }
      return $query;
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

      return response()->json([
        'suggestions' => $suggestions,
      ]);
    }

    /**
     * Method search category
     *
     * @param Request $request
     *
     * @return JsonResponse
    */
    public function searchCategories(Request $request): JsonResponse {
      $keyword = $request->input('keyword', '');
      $categories = $this->category::search("*$keyword*")->take(10)->get()->load('parent');
      return response()->json([
        'categories' => $categories
      ]);
    }
}
