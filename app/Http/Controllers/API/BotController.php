<?php

namespace App\Http\Controllers\API;

use App\Facades\BotLoginFacade;
use App\Facades\PhoneVerificationFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateLoginTokenRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BotController extends Controller
{
  /**
   * Route to create new login token
   *
   * @param CreateLoginTokenRequest $request
   *
   * @return JsonResponse
  */
  public function createToken(CreateLoginTokenRequest $request): JsonResponse {
    $phone = PhoneVerificationFacade::normalizePhone($request->input('phone'));
    $token = BotLoginFacade::generateToken($phone);
    return $this->returnSuccess([
      'token' => $token,
      'url' => config('app.front.auth_url').'/?token='.$token,
    ]);
  }

  /**
   * Route to verify login token
   *
   * @param string $token
   *
   * @return JsonResponse
  */
  public function verifyToken(string $token): JsonResponse {
    $verificationUuid = BotLoginFacade::verifyToken($token);

    if ($verificationUuid) {
      $phone = PhoneVerificationFacade::getVerifiedPhone($verificationUuid);
      $user = User::query()->where('phone', $phone)->first();
      $jwtToken = $user ? Auth::login($user) : null;
      return $this->returnSuccess([
        'verification_uuid' => $verificationUuid,
        'access_token' => $jwtToken,
        'user' => $user,
      ]);
    } else {
      return $this->returnError(__('UUID not exists'), 404);
    }
  }
}
