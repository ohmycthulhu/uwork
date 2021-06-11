<?php

namespace App\Http\Controllers\API\User;

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
use App\Http\Requests\VerifyPasswordResetRequest;
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

      if ($this->user::query()->phone($phone)->first()) {
        return $this->returnError(__('Phone is already occupied'), 403);
      }

      // Generate verification uuid
      $uuid = PhoneVerificationFacade::createSession(null, null, null, $phone);

      // Return uuid
      return $this->returnSuccess([
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
        return $this->returnError(__('Phone is not verified'), 403);
      } else {
        PhoneVerificationFacade::removeVerifiedPhone($verUuid);
      }

      if ($this->user::phone($phone)->first()) {
        return $this->returnError(__('Phone is already occupied'), 403);
      }

      $form['password'] = Hash::make($form['password']);
      $form['phone'] = $phone;

      try {
        /* @var User $user */
        $user = $this->user::create($form);
      } catch (\Exception $e) {
        // If failed, send error message
        return $this->returnError(__($e->getMessage()), 405);
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
        return $this->returnError(__('UUID not exists'), 403);
      }

      $code = $request->input('code');
      // If no verification, return error
      $verification = PhoneVerificationFacade::checkCode($uuid, $code);

      if (!$verification) {
        return $this->returnError(__('UUID not exists'), 403);
      }

      $error = $verification['error'] ?? null;
      // If no tries left, delete the entity
      if ($error) {
        return $this->returnError(__('Code is incorrect'), 405);
      }

      // If everything is okay, return success message
      $modelId = $verification['data']['id'];
      $modelClass = $verification['data']['class'];
      $phone = $verification['data']['phone'];
      if ($modelClass) {
        $model = $modelClass::query()->find($modelId);

        if ($model && $model->phone !== $phone) {
          $model->setPhone($phone, true);
        }
      }

      $flag = $modelClass ? PhoneVerificationHelper::DELETE_ON_SUCCESS : PhoneVerificationHelper::SAVE_ON_SUCCESS;
      PhoneVerificationFacade::checkCode($uuid, $code, $flag);

      // If there are deleted accounts with the same number,
      // Restore and generate the token
      if ($user = $this->user::onlyTrashed()->phone($phone)->first()) {
        $user->restore();
        $user->load(['profile', 'district', 'city', 'region']);
        if ($profile = $user->profile()->withTrashed()->first()) {
          $profile->restore();
        }
        $token = Auth::login($user);
      }

      return $this->returnSuccess([
        'user' => $user ?? $model ?? null,
        'token' => $token ?? null,
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
        return $this->returnError('Phone not exists', 403);
      }

      // If exists, check if phone number is blocked
      if (PhoneVerificationFacade::isBlocked($phoneNumber)) {
        return $this->returnError(__('Phone number is blocked. Try again in a hour'), 403);
      }

      // Adds try to phone number
      PhoneVerificationFacade::blockPhone($phoneNumber);

      $uuid = PhoneVerificationFacade::createSession($user, User::class, $user->id, $phoneNumber);

      // Send notification and save phone number as temporary blocked
      return $this->returnSuccess([
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
        return $this->returnError(__('User not exists'), 404);
      }
      $params['phone'] = $phone;

      // Try to log in
      $token = auth()->attempt($params);

      if (!$token) {
        return $this->returnError(__('Invalid credentials'), 403);
      }

      $user = Auth::user();

      // Return token and user info
      return $this->returnSuccess([
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

      return $this->returnSuccess(['user' => $user]);
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
        return $this->returnError(__('User not found'), 404);
      }

      $uuid = ResetPasswordFacade::createSession($user, !!$email, !!$phone);

      return $this->returnSuccess(['uuid' => $uuid]);
    }

    /**
     * Method for verifying password reset code
     *
     * @param VerifyPasswordResetRequest $request
     *
     * @return JsonResponse
    */
    public function verifyPasswordReset(VerifyPasswordResetRequest $request): JsonResponse {
      // Extract uuid and code
      $uuid = $request->input('uuid');
      $code = $request->input('code');

      // Try verifying the code
      $verificationResult = ResetPasswordFacade::checkCode($uuid, $code);

      // Return the result
      if ($verificationResult) {
        return $this->returnSuccess();
      } else {
        return $this->returnError(__('Provided UUID or code is invalid'), 404);
      }
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
        return $this->returnError(__('UUID is invalid or not confirmed'), 403);
      }

      $password = $request->input('password');

      /* @var ?User $user */
      $user = $this->user::query()->find($userId);

      $user->setPassword($password);

      ResetPasswordFacade::removeUuid($uuid);

      return $this->returnSuccess();
    }

    /**
     * Method to refresh access token
     *
     * @return JsonResponse
    */
    public function refreshToken(): JsonResponse {
      $access_token = auth()->refresh();
      $ttl = auth()->factory()->getTTL() * 60;
      $user = auth()->user();

      return $this->returnSuccess(compact('user', 'ttl', 'access_token'));
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
