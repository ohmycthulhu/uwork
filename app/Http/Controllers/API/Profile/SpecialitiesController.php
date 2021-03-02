<?php

namespace App\Http\Controllers\API\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\CreateSpecialityFormRequest;
use App\Http\Requests\Profile\UpdateSpecialityFormRequest;
use App\Models\User\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SpecialitiesController extends Controller
{

  /**
   * Method to create speciality
   *
   * @param CreateSpecialityFormRequest $request
   *
   * @return JsonResponse
   */
  public function create(CreateSpecialityFormRequest $request): JsonResponse
  {
    // Check if user has profile
    $profile = $this->getProfile();

    // If not, return error
    if (!$profile) {
      return response()->json(['error' => 'user does not have profile'], 403);
    }

    // Search if user has exact same speciality
    $exactSpec = $profile->specialities()
      ->exact($request->input('name'), $request->input('category_id'))
      ->first();

    // If yes, return error
    if ($exactSpec) {
      return response()->json([
        'error' => 'Similar speciality exists',
        'speciality' => $exactSpec
      ]);
    }

    // Create speciality
    $speciality = $profile->addSpeciality(
      $request->input('category_id'),
      $request->input('price'),
      $request->input('name')
    );

    // Return result
    return response()->json([
      'status' => 'success',
      'speciality' => $speciality,
    ]);
  }

  /**
   * Method to get information about specialities
   *
   * @return JsonResponse
   */
  public function get(): JsonResponse
  {
    // Check if user has profile
    $profile = $this->getProfile();

    // If not, return error
    if (!$profile) {
      return response()->json(['error' => 'user does not have profile'], 403);
    }

    $specialities = $profile->specialities()->get();

    // Return all specialities of the profile
    return response()->json([
      'specialities' => $specialities,
    ]);
  }

  /**
   * Method to update speciality
   *
   * @param UpdateSpecialityFormRequest $request
   * @param int $specialityId
   *
   * @return JsonResponse
   */
  public function update(UpdateSpecialityFormRequest $request, int $specialityId): JsonResponse
  {
    // Check if user has profile
    $profile = $this->getProfile();

    // If not, return error
    if (!$profile) {
      return response()->json(['error' => 'user does not have profile'], 403);
    }

    // Get speciality by id
    $speciality = $profile->specialities()->find($specialityId);

    // Check if speciality exists
    if (!$speciality) {
      return response()->json([
        'error' => 'Speciality doesn\'t exists',
      ], 403);
    }

    // Update speciality
    $speciality->updateInfo($request->input('price'), $request->input('name'));

    // Return the result
    return response()->json([
      'status' => 'success',
      'speciality' => $speciality,
    ]);
  }

  /**
   * Method to delete speciality
   *
   * @param int $specialityId
   *
   * @return JsonResponse
   *
   */
  public function delete(int $specialityId): JsonResponse
  {
    // Check if user has profile
    $profile = $this->getProfile();

    // If not, return error
    if (!$profile) {
      return response()->json(['error' => 'user does not have profile'], 403);
    }

    // Get speciality by id
    $speciality = $profile->specialities()->find($specialityId);
    // If exists, delete
    if ($speciality) {
      try {
        $speciality->delete();
      } catch (\Exception $e) {
        Log::error("Error on deleting speciality ($specialityId) - {$e->getMessage()}");
      }
    }

    // Return result
    return response()->json([
      'status' => 'success',
      'deleted' => !!$speciality,
    ]);
  }

  /**
   * Method to get profile of current user
   *
   * @return ?Profile
   */
  protected function getProfile(): ?Profile
  {
    $user = Auth::user();
    if (!$user) {
      return null;
    }

    return $user->profile()->first();
  }
}
