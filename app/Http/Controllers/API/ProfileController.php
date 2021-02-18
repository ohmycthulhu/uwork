<?php

namespace App\Http\Controllers\API;

use App\Helpers\PhoneVerificationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\CreateProfileRequest;
use App\Http\Requests\Profile\EditProfileRequest;
use App\Models\User\Profile;
use App\Notifications\VerifyPhoneNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
  /**
   * Method to create profile
   *
   * @param CreateProfileRequest $request
   *
   * @return JsonResponse
   */
  public function create(CreateProfileRequest $request): JsonResponse
  {
    // Get user
    $user = Auth::user();

    if ($user->profile()->first()) {
      return response()->json(['error' => 'User already has a profile'], 403);
    }

    // Get params
    $params = $request->only(['about']);
    $phone = $request->input('phone', $user->phone);
    $specialities = $request->input('specialities', []);

    // Create profile
    $profile = $user->profile()->create(array_merge($params, ['phone' => $phone]));

    // Attach specialities
    foreach ($specialities as $speciality) {
      $profile->addSpeciality($speciality['category_id'], $speciality['price']);
    }

    // Send verification code if needed
    $uuid = null;
    if ($phone === $user->phone) {
      $profile->setPhone($phone, true);
    } else {
      $uuid = PhoneVerificationHelper::createSession($user, Profile::class, $profile->id, $phone);
    }

    $profile->load(['specialities.category']);

    // Return results
    return response()->json([
      'status' => 'success',
      'profile' => $profile,
      'verification_uuid' => $uuid,
    ]);
  }
}
