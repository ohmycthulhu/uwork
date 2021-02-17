<?php

namespace App\Http\Controllers\API;

use App\Helpers\PhoneVerificationHelper;
use App\Helpers\ResetPasswordHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\PhoneVerificationRequest;
use App\Http\Requests\RegistrationFormRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SetPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
  /**
   * @var User $user
  */
    protected $user;

    /**
     * Creates new instance of controller
     *
     * @param User $user
     *
    */
    public function __construct(User $user)
    {
      $this->user = $user;
    }

    /**
     * Registers new user
     *
     * @param RegistrationFormRequest $request
     *
     * @return JsonResponse
    */
    public function register(RegistrationFormRequest $request): JsonResponse {
      // Try to create user
      $form = $request->validated();

      $form['password'] = Hash::make($form['password']);

      try {
        $user = $this->user::create($form);
      } catch (\Exception $e) {
        // If failed, send error message
        return response()
          ->json(['error' => $e->getMessage()], 405);
      }
      // Generate phone verification code and send
      $uuid = PhoneVerificationHelper::createSession($user, User::class, $user->id, $form['phone']);

      // Return user and response

      return response()->json([
        'user' => $user,
        'verification_uuid' => $uuid,
      ]);
    }

    /**
     * Method to verify number phone
     *
     * @param PhoneVerificationRequest $request
     * @param string $uuid
     *
     * @return JsonResponse
    */
    public function verifyPhoneNumber(PhoneVerificationRequest $request, string $uuid): JsonResponse {
      // Pass $uuid to facade and get result
      if (!PhoneVerificationHelper::checkUUID($uuid)) {
        return response()->json(['error' => 'UUID not exists'], 403);
      }

      $code = $request->input('code');
      // If no verification, return error
      $verification = PhoneVerificationHelper::checkCode($uuid, $code);

      if (!$verification) {
        return response()->json(['error' => 'UUID not exists'], 403);
      }

      $error = $verification['error'] ?? null;
      // If no tries left, delete the entity
      if ($error) {
        return response()->json([
          'error' => 'Code is incorrect',
          'tries_left' => $verification['tries'],
        ], 405);
      }

      // If everything is okay, return success message
      $modelId = $verification['data']['id'];
      $modelClass = $verification['data']['class'];
      $phone = $verification['data']['phone'];
      $model = $modelClass::query()->find($modelId);

      if ($model && !($model->phone === $phone && $model->phone_verified)) {
        $model->setPhone($phone, true);
      }

      return response()->json([
        'status' => 'success',
        'user' => $model,
      ]);
    }

    /**
     * Resend verification
     *
     * @param string $phoneNumber
     *
     * @return JsonResponse
    */
    public function resendVerification(string $phoneNumber): JsonResponse {
      // Check if phone exists
      $user = $this->user::query()
        ->verified(false)
        ->phone($phoneNumber)
        ->first();

      // If not exists, return error
      if (!$user) {
        return response()->json(['error' => 'Phone not exists'], 403);
      }

      // If exists, check if phone number is blocked
      if (PhoneVerificationHelper::isBlocked($phoneNumber)) {
        return response()->json([
          'error' => 'Phone number is blocked. Try again in a hour',
        ], 403);
      }

      // Adds try to phone number
      PhoneVerificationHelper::blockPhone($phoneNumber);

      $uuid = PhoneVerificationHelper::createSession($user, User::class, $user->id, $phoneNumber);

      // Send notification and save phone number as temporary blocked
      return response()->json([
        'status' => 'okay',
        'uuid' => $uuid,
      ]);
    }

    /**
     * Method to login user
     *
     * @param LoginFormRequest $request
     *
     * @return JsonResponse
    */
    public function login(LoginFormRequest $request): JsonResponse {
      // Get params
      $params = $request->validated();

      // Try to log in
      $token = auth()->attempt($params);

      if (!$token) {
        return response()->json(['error' => 'Invalid credentials'], 403);
      }

      $user = Auth::user();
      // Check if phone is verified
      if (!$user->phone_verified) {
        // If not verified, log out and return error
        return response()->json(['error' => 'Phone is not verified'], 403);
      }


      // Return token and user info
      return response()->json([
        'access_token' => $token,
        'user' => $user,
      ]);
    }

    /**
     * Method to get current user
     *
     * @return JsonResponse
    */
    public function user(): JsonResponse {
      $user = Auth::user();

      return response()->json(['user' => $user]);
    }

    /**
     * Method to reset password
     *
     * @param ResetPasswordRequest $request
     *
     * @return JsonResponse
    */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse {
      $email = $request->input('email');
      $phone = $request->input('phone');
      $query = $this->user::query();
      if ($email) {
        $query->email($email);
      }
      if ($phone) {
        $query->phone($phone);
      }
      $user = $query->first();

      if (!$user->phone_verified) {
        return response()->json(['error' => 'Phone is not confirmed'], 403);
      }

      ResetPasswordHelper::createSession($user, !!$email, !!$phone);

      return response()->json(['status' => 'success']);
    }

    /**
     * Method to set password
     *
     * @param SetPasswordRequest $request
     * @param string $uuid
     *
     * @return JsonResponse
    */
    public function setPassword(SetPasswordRequest $request, string $uuid): JsonResponse {
      $userId = ResetPasswordHelper::checkUUID($uuid);

      if (!$userId) {
        return response()->json(['error' => 'UUID is invalid'], 403);
      }

      $password = $request->input('password');
      $user = $this->user::query()->find($userId);

      $user->setPassword($password);

      ResetPasswordHelper::removeUuid($uuid);

      return response()->json(['status' => 'success']);
    }
}
