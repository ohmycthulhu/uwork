<?php

namespace App\Http\Controllers\API;

use App\Helpers\PhoneVerificationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\PhoneVerificationRequest;
use App\Http\Requests\RegistrationFormRequest;
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
      $uuid = PhoneVerificationHelper::createSession($user, $user->id, $form['phone']);

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
      $userId = $verification['data']['id'];
      $user = $this->user::query()->verified(false)->find($userId);

      if ($user) {
        $user->verifyPhone();
      }

      return response()->json([
        'status' => 'success',
        'user' => $user,
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

      $uuid = PhoneVerificationHelper::createSession($user, $user->id, $phoneNumber);

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
}
