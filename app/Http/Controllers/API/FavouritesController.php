<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User\ProfileSpeciality;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
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
   * @return JsonResponse
   */
  public function get(): JsonResponse {
    $services = Auth::user()->favouriteServices()->paginate(12);

    return response()->json([
      'services' => $services,
    ]);
  }

  /**
   * Method to add speciality as favourite
   *
   * @param string $serviceId
   *
   * @return JsonResponse
   */
  public function add(string $serviceId): JsonResponse {
    $user = Auth::user();

    // Check if service exists
    $service = $this->service::query()->with(['profile'])->find($serviceId);

    if (!$service) {
      return response()->json(['error' => 'Service does not exists'], 403);
    }

    // Get profile and check if user is owner
    if ($service->profile && $service->profile->user_id == $user->id) {
      return response()->json(['error' => 'You can not add your service as favourite'], 403);
    }

    // Attach service as favourite
    $user->favouriteServices()->attach($service->id);

    return response()->json([
      'status' => 'success'
    ]);
  }

  /**
   * Method to remove speciality from favourites
   *
   * @param string $serviceId
   *
   * @return JsonResponse
   * */
  public function remove(string $serviceId): JsonResponse {
    $user = Auth::user();

    $user->favouriteServices()->detach($serviceId);

    return response()->json([
      'status' => 'success'
    ]);
  }
}
