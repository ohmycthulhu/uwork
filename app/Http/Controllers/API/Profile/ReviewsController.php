<?php

namespace App\Http\Controllers\API\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\CreateComplaintRequest;
use App\Http\Requests\Profile\Views\CreateReviewFormRequest;
use App\Http\Requests\Profile\ReviewsRetrieveRequest;
use App\Http\Requests\Profile\Views\ReplyReviewRequest;
use App\Models\Profile\Review;
use App\Models\User;
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
  public function create(CreateReviewFormRequest $request, Profile $profile): JsonResponse
  {
    /* @var User $user */
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
      // Check if user created enough reviews for a day already
      if ($this->canCreateReview($user)) {
        $review = $profile->reviews()->create(array_merge($request->validated(), ['user_id' => $user->id]));
      } else {
        return response()->json([
          'status' => 'error',
          'error' => 'You exceed daily reviews limit'
        ], 405);
      }
    }
    $profile->synchronizeReviews();

    // Return response
    return response()->json([
      'status' => 'success',
      'review' => $review,
    ]);
  }

  /**
   * Method to reply to comments
   *
   * @param Review $review
   * @param Profile $profile
   * @param ReplyReviewRequest $request
   *
   * @return JsonResponse
   */
  public function reply(ReplyReviewRequest $request, Profile $profile, Review $review): JsonResponse
  {
    // Get profile and user
    $profile = $review->profile()->first();
    $user = Auth::user();

    // Check if user is profile's creator
    if ($profile->user_id != $user->id) {
      return response()->json([
        'status' => 'error',
        'message' => "You can't reply to the reviews on this profile"
      ], 403);
    }
    if ($review->user_id == $user->id) {
      return response()->json([
        'status' => 'error',
        'message' => "You can't reply to own reviews"
      ]);
    }

    // Create review
    $reply = $review->reply(
      $user->id,
      $request->ip(),
      $request->input('headline', ''),
      $request->input('text', '')
    );

    // Return review
    return response()->json([
      'status' => 'success',
      'review' => $reply
    ]);
  }

  /**
   * Method to add review to profile
   *
   * @param Profile $profile
   *
   * @return JsonResponse
   */
  public function delete(Profile $profile): JsonResponse
  {
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
   * @param ReviewsRetrieveRequest $request
   *
   * @return JsonResponse
   */
  public function get(ReviewsRetrieveRequest $request): JsonResponse
  {
    $user = Auth::user();
    $profile = $user->profile()->first();

    if (!$profile) {
      return response()->json([
        'reviews' => null,
      ], 404);
    }

    return $this->getById($request, $profile);
  }

  /**
   * Method to get reviews by profile id
   *
   * @param Profile $profile
   * @param ReviewsRetrieveRequest $request
   *
   * @return JsonResponse
   */
  public function getById(ReviewsRetrieveRequest $request, Profile $profile): JsonResponse
  {
    $query = $profile->reviews();

    if ($specId = $request->input('speciality_id')) {
      $query->specialityId($specId);
    }

    $reviews = $query->with(['user', 'replies.user', 'speciality'])
      ->paginate(15);

    return response()->json([
      'reviews' => $reviews
    ]);
  }

  /**
   * Method to get count of reviews by profile grouped by specialities
   *
   * @param Profile $profile
   *
   * @return JsonResponse
  */
  public function countBySpecialities(Profile $profile): JsonResponse {
    $counts = $profile->reviews()
      ->specialitiesCount()
      ->get();

    return response()->json([
      'counts' => $counts,
    ]);
  }

  /**
   * Method to check if user can create review
   *
   * @param User $user
   *
   * @return bool
  */
  protected function canCreateReview(User $user): bool {
    /* Check if user hasn't created 3 or more reviews in 24 hours */
    return $user->reviews()
        ->lastHours(24)
        ->count() < 3;
  }

  /**
   * Creates new complaint
   *
   * @param CreateComplaintRequest $request
   * @param Review $review
   *
   * @return JsonResponse
   */
  public function createComplaint(CreateComplaintRequest $request, Review $review): JsonResponse {
    /* @var User $user */
    $user = Auth::user();

    if ($user->id === $review->user_id) {
      return $this->returnError('You can\'t complaint to own profile', 403);
    }

    $complaint = $review->createComplaint(
      $user,
      $request->ip(),
      $request->input('type_id'),
      $request->input('reason_other'),
      $request->input('text')
    );

    if ($complaint) {
      // Return success if could create
      return $this->returnSuccess(compact($complaint));
    } else {
      // Otherwise, return error
      return $this->returnError('Error on creating complaint', 405);
    }
  }
}
