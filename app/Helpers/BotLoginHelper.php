<?php


namespace App\Helpers;

use App\Facades\PhoneVerificationFacade;
use App\Utils\CacheAccessor;
use Illuminate\Support\Str;

/**
 * Class for implementing login with bots
 *
*/
class BotLoginHelper
{
  /* @var CacheAccessor $storage */
  protected $storage;

  /* @var int $tokenLength */
  protected $tokenLength;

  /**
   * Initializes new object
   *
   * @param int $duration in minutes
   * @param int $tokenLength
  */
  public function __construct(int $duration = 15, int $tokenLength = 16)
  {
    $this->storage = new CacheAccessor("bot-login", null, $duration);
    $this->tokenLength = $tokenLength;
  }

  /**
   * Method to generate login token
   *
   * @param string $phone
   *
   * @return string
  */
  public function generateToken(string $phone): string {
    $token = Str::random($this->tokenLength);
    $this->storage->set($token, ['phone' => $phone]);
    return $token;
  }

  /**
   * Method to check and verify token
   *
   * @param string $token
   *
   * @return ?string
  */
  public function verifyToken(string $token): ?string {
    $phoneData = $this->storage->get($token);
    if (!$phoneData) {
      return null;
    }
    $this->storage->remove($token);
    return PhoneVerificationFacade::setVerifiedPhone($phoneData['phone']);
  }
}