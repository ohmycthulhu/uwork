<?php

namespace App\Helpers;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use App\Utils\CacheAccessor;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ResetPasswordHelper
{

  /**
   * Indicate if Nexmo is enabled
   *
   * @var bool
   */
  protected $isNexmoEnabled;

  /**
   * Cache storage
   *
   * @var CacheAccessor
  */
  protected $store;

  /**
   * Creates instance of helper
   *
   * @param bool $nexmoEnabled
   */
  public function __construct(bool $nexmoEnabled)
  {
    $this->isNexmoEnabled = $nexmoEnabled;
    $this->store = new CacheAccessor("password-reset", null, 240);
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
    $uuid = Str::random(5);

    $data = $this->generateData($user);

    if ($this->isNexmoEnabled) {
      Notification::send($user, new PasswordResetNotification($withEmail, $withPhone, $uuid));
    }

    $this->store->set($uuid, $data);

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
    $data = $this->store->get($uuid);
    return $data ? $data['id'] : null;
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
    $this->store->remove($uuid);
  }
}