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
   * Method to create new verification session
   * Returns uuid of session
   *
   * @param User $user
   * @param bool $withEmail
   * @param bool $withPhone
   *
   * @return string
  */
  public static function createSession(User $user, bool $withEmail, bool $withPhone): string {
    $uuid = \Illuminate\Support\Str::uuid();

    $data = [
      'id' => $user->id,
    ];

    Notification::send($user, new PasswordResetNotification($withEmail, $withPhone, $uuid));

    self::setCache($uuid, $data, 240);

    return $uuid;
  }

  /**
   * Method to check existence of $uuid
   *
   * @param string $uuid
   *
   * @return ?int
  */
  public static function checkUUID(string $uuid): ?int {
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
  protected static function setCache(string $uuid, array $data, int $minutes) {
    $expirationTime = now()->addMinutes($minutes);
    \Illuminate\Support\Facades\Cache::put(self::getCacheKey($uuid), $data, $expirationTime);
  }
  /**
   * Method to remove from cache
   *
   * @param string $uuid
   *
  */
  public static function removeUuid(string $uuid) {
    \Illuminate\Support\Facades\Cache::forget(self::getCacheKey($uuid));
  }

  /**
   * Method to generate cache key name by uuid
   *
   * @param string $uuid
   *
   * @return string
  */
  public static function getCacheKey(string $uuid): string {
    return "password-reset-$uuid";
  }
}