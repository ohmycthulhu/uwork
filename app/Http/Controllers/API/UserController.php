<?php

namespace App\Http\Controllers\API;

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
    $params = $request->validated();

    /* @var ?User $user */
    $user = Auth::user();

    $user->fill($params);
    $user->save();

    if ($image = $request->file('avatar')) {
      $user->setAvatar($image);
    }

    return response()->json(['user' => $user]);
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
      return response()->json(['error' => 'Password is incorrect'], 403);
    }

    $user->setPassword($newPassword);

    return response()->json(['user' => $user, 'status' => 'success']);
  }

  /**
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
      return response()->json(['error' => 'Password is incorrect'], 403);
    }

    $user->setEmail($request->input('email'));

    return response()->json(['status' => 'success', 'user' => $user]);
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
    $password = $request->input('password');

    if (!$this->checkPassword($user, $password)) {
      return response()->json(['error' => 'Password is incorrect'], 403);
    }

    $phone = $request->input('phone');

    $uuid = PhoneVerificationFacade::createSession($user, User::class, $user->id, $phone);

    return response()->json([
      'user' => $user,
      'status' => 'success',
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

    return response()->json([
      'status' => 'success',
      'user' => $user,
    ]);
  }
}
