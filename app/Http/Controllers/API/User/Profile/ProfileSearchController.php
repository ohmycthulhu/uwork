<?php

namespace App\Http\Controllers\API\User\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ProfileSearchRequest;
use App\Http\Requests\Profile\RandomProfilesRequest;
use App\Models\Categories\Category;
use App\Models\User\Profile;
use App\Models\User\ProfileSpeciality;
use App\Search\Builders\ProfileSearchBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileSearchController extends Controller
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
    // Get similar categories
    $builder = new ProfileSearchBuilder($this->profile);

    $categoryId = $request->input('category_id');
    if ($request->hasAny('categories') || $categoryId) {
      $builder->setCategories(
        $request->input('categories', []),
        $request->input('category_id')
      );
    }

    if ($request->anyFilled([
      'region_id', 'city_id',
      'district_id', 'subway_id'
    ])) {
      $builder->setLocation(
        $request->input('region_id'),
        $request->input('city_id'),
        $request->input('district_id'),
        $request->input('subway_id')
      );
    }

    if (Auth::check()) {
      $builder->setCurrentUser(Auth::id());
    }

    $priceMin = $request->input('price_min');
    $priceMax = $request->input('price_max');
    if ($priceMin != null || $priceMax) {
      $builder->setPriceRange(
        $request->input('price_min'),
        $request->input('price_max')
      );
    }

    if ($request->has('sort_by')) {
      $builder->setSorting(
        $request->input('sort_by'),
        $request->input('sort_dir', 'asc')
      );
    }

    $result = $builder->paginate(
        $request->input('per_page', 15),
        $page
      );

    $profiles = $result->getModels()->load(['specialities.category', 'user']);

    $profiles = $profiles->map(function (Profile $profile) use ($categoryId, $priceMin, $priceMax) {
      $speciality = $profile->specialities
        ->filter(function (ProfileSpeciality $speciality) use ($categoryId, $priceMin, $priceMax) {
        return $speciality->belongsToCategory($categoryId) &&
          ($priceMin == null || $speciality->price >= $priceMin) &&
          ($priceMax == null || $speciality->price <= $priceMax);
      })->first();
      return array_merge($profile->toArray(), compact('speciality'));
    });

//    if ($keyword) {
//      SearchFacade::registerSearch($keyword);
//    }

    // Return response
    return $this->returnSuccess([
      'result' => [
        'data' => $profiles,
        'total' => $result->getTotal(),
        'current_page' => $page,
        'next_page_url' => route('api.profiles.search', array_merge($request->all(), ['page' => $page + 1]))
      ]
    ]);
  }

  /**
   * Method to get random profiles
   *
   * @param RandomProfilesRequest $request
   *
   * @return JsonResponse
   */
  public function getRandom(RandomProfilesRequest $request): JsonResponse {
    $amount = $request->input('amount', 10);
    $categoryId = $request->input('category_id');
    $category = $categoryId ? $this->category::find($categoryId) : null;

    if ($categoryId && !$category) {
      return $this->returnError(__('Category not found'), 404);
    }

    $query = ProfileSpeciality::query();

    if ($categoryId) {
      $query->category($categoryId);
    }
    $profileIds = $query->groupBy('profile_id')
      ->limit($amount)
      ->inRandomOrder()
      ->whereHas('profile', function ($query) {
        return $query->public();
      })
      ->pluck('profile_id');

    $profiles = $this->profile::query()
      ->whereIn('id', $profileIds)
      ->with(['region', 'district', 'city', 'user', 'specialities'])
      ->get();

    return $this->returnSuccess([
      'profiles' => $profiles,
      'category' => $category,
    ]);
  }
}
