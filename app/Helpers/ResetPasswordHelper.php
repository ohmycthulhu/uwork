<?php

namespace App\Helpers;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use App\Notifications\VerifyPhoneNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class ResetPasswordHelper
{

  /**
   * Indicate if Nexmo is enabled
   *
   * @var bool
   */
  protected $isNexmoEnabled;

  /**
   * Creates instance of helper
   *
   * @param bool $nexmoEnabled
   */
  public function __construct(bool $nexmoEnabled)
  {
    $this->isNexmoEnabled = $nexmoEnabled;
  }


  /**
   * Method to create new verification session
   * Returns uuid of session
   *
   * @param User $user
   * @param bool $withEmail
   * @param bool $withPhone
   *
   * @return string
  */
  public function createSession(User $user, bool $withEmail, bool $withPhone): string {
    $uuid = \Illuminate\Support\Str::uuid();

    $data = $this->generateData($user);

    if ($this->isNexmoEnabled) {
      Notification::send($user, new PasswordResetNotification($withEmail, $withPhone, $uuid));
    }

    $this->setCache($uuid, $data, 240);

    return $uuid;
  }

  /**
   * Method to check existence of $uuid
   *
   * @param string $uuid
   *
   * @return ?int
  */
  public function checkUUID(string $uuid): ?int {
    $data = Cache::get(self::getCacheKey($uuid), null);
    return $data ? $data['id'] : null;
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
    \Illuminate\Support\Facades\Cache::put(self::getCacheKey($uuid), $data, $expirationTime);
  }

  /**
   * Method to generate data
   *
   * @param User $user
   *
   * @return array
  */
  protected function generateData(User $user): array {
    return [
      'id' => $user->id
    ];
  }


  /**
   * Method to remove from cache
   *
   * @param string $uuid
   *
  */
  public function removeUuid(string $uuid) {
    \Illuminate\Support\Facades\Cache::forget(self::getCacheKey($uuid));
  }

  /**
   * Method to generate cache key name by uuid
   *
   * @param string $uuid
   *
   * @return string
  */
  public function getCacheKey(string $uuid): string {
    return "password-reset-$uuid";
  }
}