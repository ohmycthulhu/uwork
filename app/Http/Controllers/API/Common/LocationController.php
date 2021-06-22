<?php

namespace App\Http\Controllers\API\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegionsLoadRequest;
use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\Region;
use App\Utils\CacheAccessor;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
  /**
   * @var Region $region
   * @var City $city
   * @var District $district
   */
  protected $region;
  protected $city;
  protected $district;
  /* @var CacheAccessor */
  protected $cacheAccessor;

  /**
   * Creates new instance of controller
   *
   * @param Region $region
   * @param City $city
   * @param District $district
   */
  public function __construct(Region $region, City $city, District $district)
  {
    $this->cacheAccessor = new CacheAccessor("location-");
    $this->region = $region;
    $this->city = $city;
    $this->district = $district;
  }


  /**
   * Get all regions
   *
   * @param RegionsLoadRequest $request
   *
   * @return JsonResponse
   */
  public function regions(RegionsLoadRequest $request): JsonResponse
  {
    $isDetailed = $request->input('detailed', true);
    $regions = $this->cacheAccessor->get(
      "regions-all-$isDetailed",
      function () use ($isDetailed) {
        $query = $this->region::query();
        if ($isDetailed) {
          $query->with('cities');
        }
        return $query->get();
      },
      true
    );
    return $this->returnSuccess(compact('regions'));
  }

  /**
   * Get information about specific region
   *
   * @param int $id
   *
   * @return JsonResponse
   */
  public function regionById(int $id): JsonResponse
  {
    $region = $this->cacheAccessor->get(
      'regions-' . $id,
      function () use ($id) {
        return $this->region::query()->with('cities')->find($id);
      },
      true
    );
    if (!$region) {
      return $this->returnError(__('Region not found'), 404);
    }

    return $this->returnSuccess(compact('region'));
  }

  /**
   * Get cities in specific region
   *
   * @param int $id
   *
   * @return JsonResponse
   */
  public function regionCities(int $id): JsonResponse
  {
    $cities = $this->cacheAccessor->get(
      "region-$id-cities",
      function () use ($id) {
        $region = $this->region::query()->find($id);
        if (!$region) {
          return null;
        }
        return $region->cities()->with('districts')->get();
      },
      true
    );
    if ($cities === null) {
      return $this->returnError(__('Region not found'), 404);
    }
    return $this->returnSuccess(compact('cities'));
  }

  /**
   * Get information about city
   *
   * @param int $id
   *
   * @return JsonResponse
   */
  public function cityById(int $id): JsonResponse
  {
    $city = $this->cacheAccessor->get(
      "cities-$id",
      function () use ($id) {
        return $this->city::query()->with(['districts', 'subways'])->find($id);
      },
      true
    );

    if (!$city) {
      return $this->returnError(__('City not found'), 404);
    }

    return $this->returnSuccess(compact('city'));
  }

  /**
   * Gets districts in city
   *
   * @param int $id
   *
   * @return JsonResponse
   */
  public function cityDistricts(int $id): JsonResponse
  {
    $districts = $this->cacheAccessor->get(
      "cities-$id-districts",
      function () use ($id) {
        $city = $this->city::query()->find($id);

        if (!$city) {
          return null;
        }

        return $city->districts()->get();
      },
      true
    );

    if ($districts === null) {
      return $this->returnError(__('City not found'), 404);
    }

    return $this->returnSuccess(compact('districts'));
  }

  /**
   * Gets subways in city
   *
   * @param int $id
   *
   * @return JsonResponse
   */
  public function citySubways(int $id): JsonResponse
  {
    $subways = $this->cacheAccessor->get(
      "cities-$id-subways",
      function () use ($id) {
        $city = $this->city::query()->find($id);

        if (!$city) {
          return $this->returnError(__('City not found'), 404);
        }

        return $city->subways()->get();
      },
      true
    );

    return $this->returnSuccess(compact('subways'));
  }
}
