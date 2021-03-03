<?php

namespace App\Http\Controllers\API\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\CreateReviewFormRequest;
use App\Models\User\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReviewsController extends Controller
{
  /**
   * Method to add review to profile
   *
   * @param CreateReviewFormRequest $request
   * @param Profile $profile
   *
   * @return JsonResponse
   */
  public function create(CreateReviewFormRequest $request, Profile $profile): JsonResponse {
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
  public function delete(Profile $profile): JsonResponse {
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
   * Method to get reviews
   *
   * @return JsonResponse
   */
  public function get(): JsonResponse {
    $user = Auth::user();
    $profile = $user->profile()->first();

    $reviews = null;
    if ($profile) {
      $reviews = $profile->reviews()->paginate(15);
    }

    return response()->json([
      'reviews' => $reviews
    ], $profile ? 200 : 404);
  }

  /**
   * Method to get reviews by profile id
   *
   * @param Profile $profile
   *
   * @return JsonResponse
  */
  public function getById(Profile $profile): JsonResponse {
    $reviews = $profile->reviews()->paginate(16);

    return response()->json([
      'reviews' => $reviews
    ]);
  }
}
