<?php

namespace App\Http\Controllers\API;

use App\Facades\PhoneVerificationFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\CreateProfileRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\RandomProfilesRequest;
use App\Models\Categories\Category;
use App\Models\Media\Image;
use App\Models\User;
use App\Models\User\Profile;
use App\Models\User\ProfileSpeciality;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
  /**
   * Object for profile
   *
   * @var Profile
  */
  protected $profile;

  /**
   * Object for category
   *
   * @var Category
  */
  protected $category;

  /**
   * Object for images
   *
   * @var Image
  */
  protected $image;

  /**
   * Create instance of controller
   *
   * @param Profile $profile
   * @param Image $image
   * @param Category $category
   */
  public function __construct(Profile $profile, Image $image, Category $category)
  {
    $this->profile = $profile;
    $this->category = $category;
    $this->image = $image;
  }

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
    /** @var ?User $user */
    $user = Auth::user();

    if ($user->profile()->first()) {
      return response()->json(['error' => 'User already has a profile'], 403);
    }

    // Get params
    $params = $request->only(['about']);
    $phone = $request->input('phone', $user->getPhone());
    $specialities = $request->input('specialities', []);

    /* @var Profile $profile */
    // Create profile
    $profile = $user->profile()->create(array_merge($params, ['phone' => $phone]));

    // Attach specialities
    foreach ($specialities as $speciality) {
      $profile->addSpeciality($speciality['category_id'], $speciality['price'], $speciality['name']);
    }

    // Send verification code if needed
    $uuid = null;
    if ($phone === $user->getPhone()) {
      $profile->setPhone($phone, true);
    } else {
      $uuid = PhoneVerificationFacade::createSession($user, Profile::class, $profile->getKey(), $phone);
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
   * Method to get my profile
   *
   * @return JsonResponse
  */
  public function get(): JsonResponse
  {
    // Get user
    /* @var ?User $user */
    $user = Auth::user();

    // Get user's profile
    $profile = $user->profile()
      ->with('specialities.category.parent', 'specialities.media', 'user', 'region', 'city', 'district')
      ->first();

    // Return profile
    return response()->json([
      'profile' => $profile
    ], $profile ? 200 : 404);
  }

  /**
   * Method to change profile information
   *
   * @param UpdateProfileRequest $request
   *
   * @return JsonResponse
  */
  public function update(UpdateProfileRequest $request): JsonResponse {
    // Get profile by user
    /* @var ?User $user */
    $user = Auth::user();

    /* @var ?Profile $profile */
    $profile = $user->profile()->first();

    // If profile doesn't exists, return error
    if (!$profile) {
      return response()->json(['error' => 'Profile not exists'], 403);
    }

    // Update about information if presented
    $profile->setInfo($request->input('about'));

    // Update phone, if presented
    $verUuid = null;
    $phone = $request->input('phone');
    if ($phone) {
      $shouldBeVerified = $profile->getPhone() !== $phone && $phone !== $user->getPhone();
      if ($shouldBeVerified) {
        $verUuid = PhoneVerificationFacade::createSession($user, Profile::class, $profile->getKey(), $phone);
      } else {
        $profile->phone = $phone;
      }
    }

    $profile->save();

    $profile->load(['specialities']);

    // Return response
    return response()->json([
      'status' => 'success',
      'profile' => $profile,
      'verification_uuid' => $verUuid,
    ]);
  }

  /**
   * Method to get profile by id
   *
   * @param string $id
   *
   * @return JsonResponse
   */
  public function getById(string $id): JsonResponse {
    // Get profile by id
    $profile = $this->profile::find($id);

    if (!$profile) {
      return response()->json(['error' => 'Profile not found'], 404);
    }

    $profile->load(['specialities.category.parent', 'specialities.media', 'user', 'region', 'city', 'district']);

    return response()->json([
      'profile' => $profile
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
      return response()->json(['status' => 'error', 'error' => 'Category not found'], 404);
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
      ->with(['region', 'district', 'city', 'user', 'speciality']);

    return response()->json([
      'status' => 'success',
      'profiles' => $profiles,
      'category' => $category,
    ]);
  }
}
