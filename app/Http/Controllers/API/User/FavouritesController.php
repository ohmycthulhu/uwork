<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ProfileSearchRequest;
use App\Models\DAO\UserFavouriteService;
use App\Models\User;
use App\Models\User\ProfileSpeciality;
use App\Search\Builders\FavouritesSearchBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FavouritesController extends Controller
{
  /**
   * Variable to hold the class of service
   *
   * @var ProfileSpeciality
   */
  protected $service;

  /**
   * Method to create instance of controller
   *
   * @param ProfileSpeciality $service
   */
  public function __construct(ProfileSpeciality $service)
  {
    $this->service = $service;
  }

  /**
   * Method to get list of favourites
   *
   * @param ProfileSearchRequest $request
   *
   * @return JsonResponse
   */
  public function get(ProfileSearchRequest $request): JsonResponse
  {
    $page = $request->input('page', 1);
    /* @var User $user */
    $user = Auth::user();
    // Get similar categories
    $builder = new FavouritesSearchBuilder(new UserFavouriteService, $user);

    $categoryId = $request->input('category_id');
    if ($request->hasAny('categories') || $categoryId) {
      $builder->setCategories(
        $request->input('categories', []),
        $request->input('category_id')
      );
    }

    if ($request->anyFilled([
      'region_id', 'city_id',
      'district_id', 'subway_id',
      'districts', 'subways'
    ])) {
      $builder->setLocation(
        $request->input('region_id'),
        $request->input('city_id'),
        $request->input('districts', $request->input('district_id')),
        $request->input('subways', $request->input('subway_id'))
      );
    }

    $priceMax = $request->input('price_max');
    if (($priceMin = $request->input('price_min')) != null || $priceMax) {
      $builder->setPriceRange(
        $priceMin,
        $priceMax
      );
    }

    $ratingMax = $request->input('rating_max');
    if (($ratingMin = $request->input('rating_min')) != null || $ratingMax != null) {
      $builder->setRatingRange($ratingMin, $ratingMax);
    }

    if ($ratings = $request->input('ratings')) {
      $builder->setRatingRanges($ratings);
    }

    if ($sortColumn = $request->has('sort_by')) {
      $builder->setSorting(
        $sortColumn,
        $request->input('sort_dir', 'asc')
      );
    }

    $result = $builder->paginate(
      $request->input('per_page', 15),
      $page
    );

    $services = $result->getModels()->load(['service.profile.user', 'service.media'])->pluck('service');
    $profiles = $services
      // Delete services with no profile
      ->filter(function (ProfileSpeciality $service) {
        return $service->profile;
      })
      // Map service to profile
      ->map(function (ProfileSpeciality $service) {
        return array_merge($service->profile->toArray(), [
          'service' => $service,
        ]);
      });

    return $this->returnSuccess([
      'result' => [
        'data' => $profiles,
        'total' => $result->getTotal(),
        'current_page' => $page,
        'next_page_url' => route('api.user.fav.list', array_merge($request->all(), ['page' => $page + 1]))
      ]
    ]);
  }

  /**
   * Method to add speciality as favourite
   *
   * @param string $serviceId
   *
   * @return JsonResponse
   */
  public function add(string $serviceId): JsonResponse
  {
    $user = Auth::user();

    // Check if service exists
    $service = $this->service::query()->with(['profile'])->find($serviceId);

    if (!$service) {
      return $this->returnError(__('Service does not exists'), 403);
    }

    // Get profile and check if user is owner
    if ($service->profile && $service->profile->user_id == $user->id) {
      return $this->returnError(__('You can not add your service as favourite'), 403);
    }

    // Attach service as favourite
    $user->favouriteServices()->create(['service_id' => $service->id]);

    return $this->returnSuccess();
  }

  /**
   * Method to remove speciality from favourites
   *
   * @param string $serviceId
   *
   * @return JsonResponse
   * */
  public function remove(string $serviceId): JsonResponse
  {
    $user = Auth::user();

    $service = $user->favouriteServices()->serviceId($serviceId)->first();
    if ($service) {
      $service->delete();
    }

    return $this->returnSuccess();
  }
}
