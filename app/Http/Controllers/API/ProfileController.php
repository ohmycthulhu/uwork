<?php

namespace App\Http\Controllers\API;

use App\Helpers\PhoneVerificationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\AddViewRequest;
use App\Http\Requests\Profile\CreateProfileRequest;
use App\Http\Requests\Profile\CreateReviewFormRequest;
use App\Http\Requests\Profile\EditProfileRequest;
use App\Models\Media\Image;
use App\Models\Profile\ProfileView;
use App\Models\Profile\Review;
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

    // Attach images
    $images = $request->input('images', []);
    Image::attachMedia(Profile::class, $profile->id, $images);

    $avatar = $request->file('avatar');
    if ($avatar) {
      $profile->setAvatar($avatar);
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

  /**
   * Method to change profile information
   *
   * @param EditProfileRequest $request
   *
   * @return JsonResponse
  */
  public function update(EditProfileRequest $request): JsonResponse {
    // Get profile by user
    $user = Auth::user();
    $profile = $user->profile()->first();

    // If profile doesn't exists, return error
    if (!$profile) {
      return response()->json(['error' => 'Profile not exists'], 403);
    }

    // If picture is sent, update avatar
    $avatar = $request->file('avatar');
    if ($avatar) {
      $profile->setAvatar($avatar);
    }

    // If "images" are set, remove all images not presented in profile
    $images = $request->input('images');

    if ($images !== null) {
      $profile->media()->whereNotIn('id', $images)->delete();

      // And add ones, who are not attached yet
      Image::attachMedia(Profile::class, $profile->id, $images);
    }

    // Update about information if presented
    $about = $request->input('about');
    if ($about) $profile->about = $about;

    // Update phone, if presented
    $verUuid = null;
    $phone = $request->input('phone');
    if ($phone) {
      $shouldBeVerified = $profile->phone !== $phone && $phone !== $user->phone;
      if ($shouldBeVerified) {
        $verUuid = PhoneVerificationHelper::createSession($user, Profile::class, $profile->id, $phone);
      } else {
        $profile->phone = $phone;
      }
    }

    $profile->save();

    // Remove specialities, that needs to be removed
    $specialitiesToRemove = $request->input('remove_specialities', []);
    foreach ($specialitiesToRemove as $speciality) {
      $profile->removeSpeciality($speciality);
    }

    // Add specialities, that needs to be added
    $specialitiesToAdd = $request->input('add_specialities', []);
    foreach ($specialitiesToAdd as $speciality) {
      $profile->addSpeciality($speciality['category_id'], $speciality['price']);
    }

    $profile->load(['media', 'specialities']);

    // Return response
    return response()->json([
      'status' => 'success',
      'profile' => $profile,
      'verification_uuid' => $verUuid,
    ], 200);
  }

  /**
   * Method to add review to profile
   *
   * @param CreateReviewFormRequest $request
   * @param Profile $profile
   *
   * @return JsonResponse
  */
  public function createReview(CreateReviewFormRequest $request, Profile $profile): JsonResponse {
    $user = Auth::user();
    // Check if profile doesn't belongs to user
    if ($profile->user_id == $user->id) {
      return response()->json(['error' => 'You can\'t review own profile'], 403);
    }

    // Check if user already left review on the profile
    $existingReview = $profile->reviews()->userId($user->id)->first();
    if ($existingReview) {
      // Edit review
      $review = $existingReview;
      $review->fill($request->validated());
    } else {
      // Create review
      $review = $profile->reviews()->create(array_merge($request->validated(), ['user_id' => $user->id]));
    }
    $profile->synchronizeReviews();

    // Return response
    return response()->json([
      'status' => 'success',
      'review' => $review,
    ]);
  }

  /**
   * Method to add review to profile
   *
   * @param Profile $profile
   *
   * @return JsonResponse
  */
  public function deleteReview(Profile $profile): JsonResponse {
    $user = Auth::user();

    // Check if user has review on this profile
   $review = $profile->reviews()->userId($user->id)->first();

   if (!$review) {
     return response()->json(['error' => 'You don\'t have review on the profile'], 403);
   }

   $review->delete();

   $profile->synchronizeReviews();

   return response()->json([
     'status' => 'success'
   ]);
  }

  /**
   * Method to add view
   *
   * @param AddViewRequest $request
   * @param Profile $profile
   *
   * @return JsonResponse
  */
  public function addView(AddViewRequest $request, Profile $profile): JsonResponse {
    $user = Auth::user();

    if ($user->id == $profile->user_id) {
      return response()->json(['error' => 'You can\'t view own profile'], 403);
    }

    $view = ProfileView::make($profile, $user, $request->ip(), $request->input('opened', false));

    $profile->synchronizeViews();

    return response()->json([
      'status' => 'success',
      'view' => $view,
    ]);
  }
}
