<?php

namespace App\Helpers;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use App\Utils\CacheAccessor;
use Illuminate\Support\Facades\Notification;

class ResetPasswordHelper extends VerificationHelperBase
{
  /**
   * Cache storage
   *
   * @var CacheAccessor
  */
  protected $store;

  /**
   * Creates instance of helper
   *
   * @param bool $isVerificationEnabled
   * @param bool $nexmoEnabled
   */
  public function __construct(bool $isVerificationEnabled, bool $nexmoEnabled)
  {
    parent::__construct($isVerificationEnabled, $nexmoEnabled);

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
    $uuid = $this->generateUUID();
    $code = $this->generateCode();

    $data = $this->generateData($user, $code);

    if ($this->isNexmoEnabled) {
      Notification::send($user, new PasswordResetNotification($withEmail, $withPhone, $uuid, $code));
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
   * @param int    $successFlag
   *
   * @return bool
  */
  public function checkCode(string $uuid, string $code, int $successFlag = self::NOTHING_ON_SUCCESS): bool {
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