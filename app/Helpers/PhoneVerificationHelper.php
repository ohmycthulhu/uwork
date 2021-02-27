<?php

namespace App\Helpers;

use App\Models\User;
use App\Notifications\VerifyPhoneNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class PhoneVerificationHelper
{
  /**
   * Indicates if verification is being done or ignored
   *
   * @var bool
  */
  protected $verificationEnabled;

  /**
   * Indicate if Nexmo is enabled
   *
   * @var bool
  */
  protected $isNexmoEnabled;

  /**
   * Creates instance of helper
   *
   * @param bool $verificationEnabled
   * @param bool $nexmoEnabled
  */
  public function __construct(bool $verificationEnabled, bool $nexmoEnabled)
  {
    $this->verificationEnabled = $verificationEnabled;
    $this->isNexmoEnabled = $nexmoEnabled;
  }

  /**
   * Method to create new verification session
   * Returns uuid of session
   *
   * @param User $user
   * @param string $class
   * @param int $id
   * @param string $phone
   *
   * @return string
  */
  public function createSession(User $user, string $class, int $id, string $phone): string {
    $uuid = \Illuminate\Support\Str::uuid();

    $code = \Illuminate\Support\Str::random(6);

    $data = $this->generateData($class, $id, $phone, $code);

    if ($this->isNexmoEnabled) {
      Notification::send($user, new VerifyPhoneNotification($code));
    }

    $this->setCache($uuid, $data, 10);

    return $uuid;
  }

  /**
   * Method to check existence of $uuid
   *
   * @param string $uuid
   *
   * @return bool
  */
  public function checkUUID(string $uuid): bool {
    return Cache::has($this->getCacheKey($uuid));
  }

  /**
   * Method to check if code is correct
   *
   * @param string $uuid
   * @param string $code
   * @param bool $deleteOnSuccess
   *
   * @return array|bool
  */
  public function checkCode(string $uuid, string $code, bool $deleteOnSuccess = false) {
    if (!$this->checkUUID($uuid)) {
      return false;
    }

    $data = Cache::get($this->getCacheKey($uuid));

    if (!$data) {
      return false;
    }

    if ($this->verificationEnabled && $data['code'] !== $code) {
      $triesLeft = $data['tries'] - 1;
      if ($triesLeft > 0) {
        $data['tries'] = $triesLeft;
        $this->setCache($uuid, $data, 10);
      } else {
        $this->removeCache($uuid);
      }
      return ['error' => true, 'tries' => $triesLeft];
    }

    if ($deleteOnSuccess) {
      $this->removeCache($uuid);
    }

    return ['data' => $data];
  }

  /**
   * Check if phone is blocked
   *
   * @param string $phone
   *
   * @return bool
  */
  public function isBlocked(string $phone): bool {
    return Cache::get("blocked-phone-$phone") >= 3;
  }

  /**
   * BLock the phone
   *
   * @param string $phone
  */
  public function blockPhone(string $phone) {
    $key = "blocked-phone-$phone";
    if (Cache::has($key)) {
      Cache::increment("blocked-phone-$phone");
    } else {
      Cache::put("blocked-phone-$phone", 1, now()->addHour());
    }
  }

  /**
   * Method to generate data to save
   *
   * @param string $class
   * @param int $id
   * @param string $phone
   * @param string $code
   *
   * @return array
  */
  protected function generateData(string $class, int $id, string $phone, string $code): array {
    return [
      'phone' => $phone,
      'class' => $class,
      'id' => $id,
      'code' => $code,
      'tries' => 3,
    ];
  }


  /**
   * Method to set in cache
   *
   * @param string $uuid
   * @param array $data
   * @param int $minutes
   *
  */
  protected function setCache(string $uuid, array $data, int $minutes) {
    $expirationTime = now()->addMinutes($minutes);
    \Illuminate\Support\Facades\Cache::put($this->getCacheKey($uuid), $data, $expirationTime);
  }

  /**
   * Method to remove from cache
   *
   * @param string $uuid
   *
  */
  protected function removeCache(string $uuid) {
    \Illuminate\Support\Facades\Cache::forget($this->getCacheKey($uuid));
  }

  /**
   * Method to generate cache key name by uuid
   *
   * @param string $uuid
   *
   * @return string
  */
  public function getCacheKey(string $uuid): string {
    return "phone-verification-$uuid";
  }
}