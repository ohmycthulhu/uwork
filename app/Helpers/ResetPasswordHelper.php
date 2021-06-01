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
   * Indicates whether verification is active or not
   *
   * @var bool
  */
  protected $verificationEnabled;

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
   * @param bool $isVerificationEnabled
   */
  public function __construct(bool $nexmoEnabled, bool $isVerificationEnabled)
  {
    $this->isNexmoEnabled = $nexmoEnabled;
    $this->verificationEnabled = $isVerificationEnabled;
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
    $uuid = Str::uuid();
    $code = Str::random(6);

    $data = $this->generateData($user, $code);

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
    return $data && ($data['verified'] ?? false) ? $data['id'] : null;
  }

  /**
   * Method to verify the code for uuid
   *
   * @param string $uuid
   * @param string $code
   *
   * @return bool
  */
  public function verifyUUID(string $uuid, string $code): bool {
    // Retrieve the data
    $data = $this->store->get($uuid);

    // If data not exists, return false
    if (!$data) {
      return false;
    }

    // If already verified, return true
    if ($data['verified'] ?? false) {
      return true;
    }

    // Check if the stored code equals to the provided
    $shouldBeVerified = !$this->verificationEnabled || $data['code'] == $code;

    if ($shouldBeVerified) {
      $data['verified'] = true;
      $this->store->set($uuid, $data);
    }

    // Return verification result
    return $shouldBeVerified;
  }

  /**
   * Method to generate data
   *
   * @param User $user
   * @param string $code
   *
   * @return array
  */
  protected function generateData(User $user, string $code): array {
    return [
      'id' => $user->id,
      'code' => $code,
      'verified' => false,
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