<?php

namespace App\Http\Controllers\API\User\Profile;

use App\Facades\NotificationFacade;
use App\Facades\PhoneVerificationFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\CreateComplaintRequest;
use App\Http\Requests\Profile\CreateProfileRequest;
use App\Http\Requests\Profile\RandomProfilesRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
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
      return $this->returnError(__('User already has a profile'), 403);
    }

    // Get params
    $phone = $request->input('phone', $user->getPhone());
    $specialities = $request->input('specialities', []);
    $params = [
      'about' => $request->input('about', $user->getAbout()),
      'phone' => $request->input('phone', $user->getPhone()),
    ];

    /* @var Profile $profile */
    // Create profile
    $profile = $user->profile()->create($params);

    // Attach specialities
    foreach ($specialities as $speciality) {
      $profile->addSpeciality(
        $speciality['category_id'],
        $speciality['price'] ?? null,
        $speciality['name'] ?? '',
        $speciality['description'] ?? '',
        $speciality['images'] ?? null,
      );
    }

    // Send verification code if needed
    $uuid = null;
    if ($phone === $user->getPhone()) {
      $profile->setPhone($phone, true);
    } else {
      $uuid = PhoneVerificationFacade::createSession($user, Profile::class, $profile->getKey(), $phone);
    }

    $profile->load(['specialities.category']);

    NotificationFacade::create(
      $user,
      Profile::class,
      $profile->id,
      'Профиль отправлен на проверку',
      null
    );

    // Return results
    return $this->returnSuccess([
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
    return $this->returnSuccess([
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
      return $this->returnError(__('Profile not exists'), 403);
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

    if ($profile->confirmed_at) {
      $profile->confirmed_at = null;
      NotificationFacade::create(
        $user,
        Profile::class,
        $profile->id,
        'Профиль отправлен на проверку',
        null
      );
    }

    $profile->save();

    $profile->load(['specialities']);

    // Return response
    return $this->returnSuccess([
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
      return $this->returnError(__('Profile not found'), 404);
    }

    $profile->load(['specialities', 'specialities.media', 'user', 'region', 'city', 'district']);

    $specialities = ProfileSpeciality::includeCategoriesPath($profile['specialities']);

    return $this->returnSuccess([
      'profile' => array_merge(
        $profile->toArray(),
        compact('specialities')
      )
    ]);
  }

  /**
   * Creates new complaint
   *
   * @param CreateComplaintRequest $request
   * @param Profile $profile
   *
   * @return JsonResponse
  */
  public function createComplaint(CreateComplaintRequest $request, Profile $profile): JsonResponse {
    /* @var User $user */
    $user = Auth::user();

    if ($user->id == $profile->user_id) {
      return $this->returnError('You can\'t complaint to own profile', 403);
    }

    $complaint = $profile->createComplaint(
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
      return $this->returnError(__('Error on creating complaint'), 405);
    }
  }
}
