<?php

namespace App\Http\Controllers\API;

use App\Helpers\PhoneVerificationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ChangeEmailRequest;
use App\Http\Requests\Profile\ChangeNameRequest;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\ChangePhoneRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //
  /**
   * Method to change non-primary user information (e.g. names)
   *
   * @param ChangeNameRequest $request
   *
   * @return JsonResponse
  */
  public function changeProfile(ChangeNameRequest $request): JsonResponse {
    $params = $request->validated();

    $user = Auth::user();

    $user->fill($params);
    $user->save();

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
    $user = Auth::user();
    $password = $request->input('password');

    if (!$this->checkPassword($user, $password)) {
      return response()->json(['error' => 'Password is incorrect'], 403);
    }

    $user->email = $request->input('email');
    $user->save();

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
    $user = Auth::user();
    $password = $request->input('password');

    if (!$this->checkPassword($user, $password)) {
      return response()->json(['error' => 'Password is incorrect'], 403);
    }

    $phone = $request->input('phone');

    $uuid = PhoneVerificationHelper::createSession($user, User::class, $user->id, $phone);

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
    return Auth::validate(['phone' => $user->phone, 'password' => $password]);
  }
}
