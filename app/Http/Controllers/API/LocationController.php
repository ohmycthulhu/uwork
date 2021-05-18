<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\Region;
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

  /**
   * Creates new instance of controller
   *
   * @param Region $region
   * @param City $city
   * @param District $district
   */
  public function __construct(Region $region, City $city, District $district)
  {
    $this->region = $region;
    $this->city = $city;
    $this->district = $district;
  }


  /**
   * Get all regions
   *
   * @return JsonResponse
   */
  public function regions(): JsonResponse {
    $regions = $this->region::query()->with('cities')->get();

    return $this->returnSuccess(['regions' => $regions]);
  }

  /**
   * Get information about specific region
   *
   * @param int $id
   *
   * @return JsonResponse
   */
  public function regionById(int $id): JsonResponse {
    $region = $this->region::query()->with('cities')->find($id);
    if (!$region) {
      return $this->returnError(__('Region not found'), 404);
    }

    return $this->returnSuccess(['region' => $region]);
  }

  /**
   * Get cities in specific region
   *
   * @param int $id
   *
   * @return JsonResponse
   */
  public function regionCities(int $id): JsonResponse {
    $region = $this->region::query()->find($id);
    if (!$region) {
      return $this->returnError(__('Region not found'), 404);
    }
    $cities = $region->cities()->with('districts')->get();

    return $this->returnSuccess(['cities' => $cities]);
  }

  /**
   * Get information about city
   *
   * @param int $id
   *
   * @return JsonResponse
   */
  public function cityById(int $id): JsonResponse {
    $city = $this->city::query()->with('districts')->find($id);

    if (!$city) {
      return $this->returnError(__('City not found'), 404);
    }

    return $this->returnSuccess(['city' => $city]);
  }

  /**
   * Gets districts in city
   *
   * @param int $id
   *
   * @return JsonResponse
   */
  public function cityDistricts(int $id): JsonResponse {
    $city = $this->city::query()->find($id);

    if (!$city) {
      return $this->returnError(__('City not found'), 404);
    }

    $districts = $city->districts()->get();

    return $this->returnSuccess(['districts' => $districts]);
  }
}
