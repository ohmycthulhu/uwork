<?php

namespace App\Http\Controllers\API\User;

use App\Facades\PhoneVerificationFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangeEmailRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangePhoneRequest;
use App\Http\Requests\User\ChangeUserInfoRequest;
use App\Http\Requests\User\UpdateSettingsRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
  /**
   * Method to change non-primary user information (e.g. names)
   *
   * @param ChangeUserInfoRequest $request
   *
   * @return JsonResponse
  */
  public function changeProfile(ChangeUserInfoRequest $request): JsonResponse {
    /* @var ?User $user */
    $user = Auth::user();

    $user->fill($request->validated());
    $user->save();

    if ($image = $request->file('avatar')) {
      $user->setAvatar($image);
    }

    return $this->returnSuccess(compact('user'));
  }

  /**
   * Method to change password
   *
   * @param ChangePasswordRequest $request
   *
   * @return JsonResponse
  */
  public function changePassword(ChangePasswordRequest $request): JsonResponse {
    $password = $request->input('current_password');
    $newPassword = $request->input('password');

    /* @var ?User $user */
    $user = Auth::user();

    if (!$this->checkPassword($user, $password)) {
      return $this->returnError(__('Password is incorrect'), 403);
    }

    $user->setPassword($newPassword);

    return $this->returnSuccess(compact('user'));
  }

  /**
   * Note: It's deprecated
   * Method to change email
   *
   * @param ChangeEmailRequest $request
   *
   * @return JsonResponse
  */
  public function changeEmail(ChangeEmailRequest $request): JsonResponse {
    /* @var ?User $user */
    $user = Auth::user();
    $password = $request->input('password');

    if (!$this->checkPassword($user, $password)) {
      return $this->returnError(__('Password is incorrect'), 403);
    }

    $user->setEmail($request->input('email'));

    return $this->returnSuccess(compact('user'));
  }

  /**
   * Method to change phone number
   *
   * @param ChangePhoneRequest $request
   *
   * @return JsonResponse
  */
  public function changePhone(ChangePhoneRequest $request): JsonResponse {
    /* @var ?User $user */
    $user = Auth::user();

    $verUuid = $request->input('verification_uuid');
    if ($user->phone !== PhoneVerificationFacade::getVerifiedPhone($verUuid)) {
      return $this->returnError(__('Old phone number is not verified'), 405);
    }

    PhoneVerificationFacade::removeVerifiedPhone($verUuid);

    $phoneNew = PhoneVerificationFacade::normalizePhone($request->input('phone'));

    $uuid = PhoneVerificationFacade::createSession($user, User::class, $user->id, $phoneNew);

    return $this->returnSuccess([
      'user' => $user,
      'message' => 'Code sent to confirm',
      'verification_uuid' => $uuid,
    ]);
  }

  /**
   * Method to check if current password is correct
   *
   * @param User $user
   * @param string $password
   *
   * @return bool
  */
  protected function checkPassword(User $user, string $password): bool {
    return Auth::validate(['phone' => $user->getPhone(), 'password' => $password]);
  }

  /**
   * Method to update settings
   *
   * @param UpdateSettingsRequest $request
   *
   * @return JsonResponse
  */
  public function updateSettings(UpdateSettingsRequest $request): JsonResponse {
    /* @var ?User $user */
    $user = Auth::user();
    $settings = $request->input('settings', []);
    foreach ($settings as $setting => $value) {
      $user->setSetting($setting, !!$value);
    }
    $user->save();

    return $this->returnSuccess(compact('user'));
  }

  /**
   * Route for deleting the account
   *
   * @return JsonResponse
  */
  public function delete(): JsonResponse {
    // Get user and the profile
    /* @var User $user */
    $user = Auth::user();
    $profile = $user->profile()->first();

    try {
      if ($profile) {
        // Delete profile if exists
        $profile->forceDelete();
      }

      // Delete user
      $user->forceDelete();
    } catch (\Exception $exception) {
      return $this->returnError(__($exception->getMessage()), 503);
    }

    return $this->returnSuccess();
  }

  /**
   * Function for deleting the avatar
   *
   * @return JsonResponse
  */
  public function removeAvatar(): JsonResponse {
    /* @var User $user */
    $user = Auth::user();

    $user->removeAvatar();

    return $this->returnSuccess(compact('user'));
  }
}
