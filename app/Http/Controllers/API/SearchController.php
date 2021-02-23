<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileSearchRequest;
use App\Models\Categories\Category;
use App\Models\User\Profile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    protected $category;
    protected $profile;

    /**
     * Creates class instance
     *
     * @param Profile $profile
     * @param Category $category
    */
    public function __construct(Profile $profile, Category $category)
    {
      $this->profile = $profile;
      $this->category = $category;
    }

    /**
     * Performs search
     *
     * @param ProfileSearchRequest $request
     *
     * @return JsonResponse
    */
    public function search(ProfileSearchRequest $request): JsonResponse {
      // Create query
      $query = $this->profile::query()
        ->with(['specialities.category', 'user']);

      // Add constraint by keyword, if exists
      if ($keyword = $request->input('keyword')) {
        $query = $this->setKeywordConstraint($query, $keyword);
      }

      // Add constraint by category_id, if exists
      if ($categoryId = $request->input('category_id')) {
        $query = $this->setCategoryConstraint($query, $categoryId);
      }

      if ($user = Auth::user()) {
        $query->notUser($user);
      }

      // Add constraint by region, city and district
      $this->setLocationConstraints(
        $query,
        $request->input('region_id'),
        $request->input('city_id'),
        $request->input('district_id')
      );

      // Return response
      return response()->json([
        'result' => $query->paginate(
          $request->input('per_page', 12)
        )
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
}
