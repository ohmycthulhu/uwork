<?php

namespace App\Http\Controllers\API;

use App\Facades\PhoneVerificationFacade;
use App\Facades\ResetPasswordFacade;
use App\Helpers\PhoneVerificationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginFormRequest;
use App\Http\Requests\Authentication\PhoneVerificationRequest;
use App\Http\Requests\Authentication\RegisterPhoneRequest;
use App\Http\Requests\Authentication\RegistrationFormRequest;
use App\Http\Requests\Authentication\ResetPasswordRequest;
use App\Http\Requests\User\SetPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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
     * Method to ask for phone verification
     *
     * @param RegisterPhoneRequest $request
     *
     * @return JsonResponse
    */
    public function promptPhone(RegisterPhoneRequest $request): JsonResponse {
      $phone = PhoneVerificationFacade::normalizePhone($request->input('phone'));

      // Generate verification uuid
      $uuid = PhoneVerificationFacade::createSession(null, null, null, $phone);

      // Return uuid
      return response()->json([
        'verification_uuid' => $uuid,
      ]);
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

      $verUuid = $form['verification_uuid'];
      $phone = PhoneVerificationFacade::getVerifiedPhone($verUuid);

      if (!$phone) {
        return response()->json(['error' => 'Phone is not verified'], 403);
      } else {
        PhoneVerificationFacade::removeVerifiedPhone($verUuid);
      }

      if ($this->user::phone($phone)->first()) {
        return response()->json(['error' => 'Phone is already occupied'], 403);
      }

      $form['password'] = Hash::make($form['password']);
      $form['phone'] = $phone;

      try {
        /* @var User $user */
        $user = $this->user::create($form);
      } catch (\Exception $e) {
        // If failed, send error message
        return $this->returnError($e->getMessage(), 405);
      }

      if ($avatar = $request->file('avatar')) {
        $user->setAvatar($avatar);
      }

      $token = Auth::login($user);

      // Return user and response
      return $this->returnSuccess(compact('user', 'token'));
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
      if (!PhoneVerificationFacade::checkUUID($uuid)) {
        return response()->json(['error' => 'UUID not exists'], 403);
      }

      $code = $request->input('code');
      // If no verification, return error
      $verification = PhoneVerificationFacade::checkCode($uuid, $code);

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
      $model = null;
      if ($modelClass) {
        $model = $modelClass::query()->find($modelId);

        if ($model && $model->phone !== $phone) {
          $model->setPhone($phone, true);
        }
      }

      $flag = $modelClass ? PhoneVerificationHelper::DELETE_ON_SUCCESS : PhoneVerificationHelper::SAVE_ON_SUCCESS;
      PhoneVerificationFacade::checkCode($uuid, $code, $flag);

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
      if (PhoneVerificationFacade::isBlocked($phoneNumber)) {
        return response()->json([
          'error' => 'Phone number is blocked. Try again in a hour',
        ], 403);
      }

      // Adds try to phone number
      PhoneVerificationFacade::blockPhone($phoneNumber);

      $uuid = PhoneVerificationFacade::createSession($user, User::class, $user->id, $phoneNumber);

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
      $email = $params['email'] ?? null;
      $phone = ($params['phone'] ?? false) ? PhoneVerificationFacade::normalizePhone($params['phone']) : null;

      if (!$this->checkUserExists($email, $phone)) {
        return response()->json(['error' => 'User not exists'], 404);
      }

      // Try to log in
      $token = auth()->attempt($params);

      if (!$token) {
        return response()->json(['error' => 'Invalid credentials'], 403);
      }

      $user = Auth::user();

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
      $user = Auth::user()->load(['profile', 'district', 'city', 'region']);

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

      /* @var ?User $user */
      $user = $query->first();

      if (!$user) {
        return response()->json(['status' => 'error', 'error' => 'User not found'], 404);
      }

      $uuid = ResetPasswordFacade::createSession($user, !!$email, !!$phone);

      return response()->json(['status' => 'success', 'uuid' => $uuid]);
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
      $userId = ResetPasswordFacade::checkUUID($uuid);

      if (!$userId) {
        return response()->json(['error' => 'UUID is invalid'], 403);
      }

      $password = $request->input('password');

      /* @var ?User $user */
      $user = $this->user::query()->find($userId);

      $user->setPassword($password);

      ResetPasswordFacade::removeUuid($uuid);

      return response()->json(['status' => 'success']);
    }

    /**
     * Check if user exists
     *
     * @param ?string $email
     * @param ?string $phone
     *
     * @return bool
    */
    protected function checkUserExists(?string $email, ?string $phone): bool {
      $q = $this->user::query();

      if ($email) {
        $q->email($email);
      }
      if ($phone) {
        $q->phone($phone);
      }

      return !!$q->first();
    }
}
