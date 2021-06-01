<?php

namespace App\Helpers;

use App\Models\User;
use App\Notifications\VerifyPhoneNotification;
use App\Utils\CacheAccessor;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Nutnet\LaravelSms\SmsSender;

class PhoneVerificationHelper extends VerificationHelperBase
{
  /**
   * Store accessor for verification
   * @var CacheAccessor
  */
  protected $storeVerifying;

  /**
   * Store accessor for already verified numbers
   * @var CacheAccessor
  */
  protected $storeVerified;

  /**
   * Store accessor for blocked numbers
   * @var CacheAccessor
  */
  protected $storeBlocked;

  /**
   * Creates instance of helper
   *
   * @param bool $verificationEnabled
   * @param bool $nexmoEnabled
   */
  public function __construct(bool $verificationEnabled, bool $nexmoEnabled)
  {
    parent::__construct($verificationEnabled, $nexmoEnabled);

    $this->storeVerifying = new CacheAccessor("phone-verifying", null, 10);
    $this->storeVerified = new CacheAccessor("phone-verified", null, 30);
    $this->storeBlocked = new CacheAccessor("phone-blocked", 0, 60);
  }

  /**
   * Method to create new verification session
   * Returns uuid of session
   *
   * @param ?User $user
   * @param ?string $class
   * @param ?int $id
   * @param string $phone
   *
   * @return string
   */
  public function createSession(?User $user, ?string $class, ?int $id, string $phone): string
  {
    $uuid = $this->generateUUID();

    $code = $this->generateCode();

    $data = $this->generateData($class, $id, $phone, $code);

    if ($this->isNexmoEnabled) {
      if ($user) {
        Notification::send($user, new VerifyPhoneNotification($code));
      } else {
        if (config('app.env') !== 'testing') {
          /* @var SmsSender $sender*/
          $sender = app(SmsSender::class);
          $sender->send($phone, "Your verification code is $code");
        }
      }
    }

    $this->storeVerifying->set($uuid, $data);

    return $uuid;
  }

  /**
   * Method to generate trusted verification uuid
   *
   * @param string $phone
   *
   * @return string
  */
  public function setVerifiedPhone(string $phone): string {
    $uuid = Str::uuid();
    $this->storeVerified->set($uuid, ['phone' => $phone]);
    return $uuid;
  }

  /**
   * Method to check existence of $uuid
   *
   * @param string $uuid
   *
   * @return bool
   */
  public function checkUUID(string $uuid): bool
  {
    return !!$this->storeVerifying->get($uuid);
  }

  /**
   * Method to check if code is correct
   *
   * @param string $uuid
   * @param string $code
   * @param int $successFlag
   *
   * @return array|bool
   */
  public function checkCode(string $uuid, string $code, int $successFlag = self::NOTHING_ON_SUCCESS)
  {
    if (!$this->checkUUID($uuid)) {
      return false;
    }

    $data = $this->storeVerifying->get($uuid);

    if (!$data) {
      return false;
    }

    if ($this->verificationEnabled && $data['code'] !== $code) {
      $triesLeft = $data['tries'] - 1;
      if ($triesLeft > 0) {
        $data['tries'] = $triesLeft;
        $this->storeVerifying->set($uuid, $data);
      } else {
        $this->storeVerifying->remove($uuid);
      }
      return ['error' => true, 'tries' => $triesLeft];
    }

    switch ($successFlag) {
      case self::SAVE_ON_SUCCESS:
        $phone = $data['phone'];
        $this->setPhoneVerified($uuid, $phone);
        break;
      case self::DELETE_ON_SUCCESS:
        $this->storeVerifying->remove($uuid);
        break;
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
  public function isBlocked(string $phone): bool
  {
    return $this->storeBlocked->get($phone) >= 3;
  }

  /**
   * Verified phones section
   */

  /**
   * Check if phone verified (by uuid)
   *
   * @param string $uuid
   *
   * @return ?string
   */
  public function getVerifiedPhone(string $uuid): ?string
  {
    $stored = $this->storeVerified->get($uuid);

    if (!$stored) {
      return null;
    }

    return $stored['phone'] ?? "";
  }

  /**
   * Mark phone as verified
   *
   * @param string $uuid
   * @param string $phone
   *
   * @return void
   */
  protected function setPhoneVerified(string $uuid, string $phone)
  {
    $data = [
      'phone' => $phone
    ];
    $this->storeVerified->set($uuid, $data);
  }

  /**
   * Remove verification information
   *
   * @param string $uuid
   *
   * @return void
   */
  public function removeVerifiedPhone(string $uuid)
  {
    $this->storeVerified->remove($uuid);
  }

  /**
   * Block the phone
   *
   * @param string $phone
   */
  public function blockPhone(string $phone)
  {
    $this->storeBlocked
      ->set(
        $phone,
        $this->storeBlocked->get($phone) + 1
      );
  }

  /**
   * Method to generate data to save
   *
   * @param ?string $class
   * @param ?int $id
   * @param string $phone
   * @param string $code
   *
   * @return array
   */
  protected function generateData(?string $class, ?int $id, string $phone, string $code): array
  {
    return [
      'phone' => $phone,
      'class' => $class,
      'id' => $id,
      'code' => $code,
      'tries' => 3,
    ];
  }

  /**
   * Method for normalizing phone number
   *
   * @param string $phone
   *
   * @return string
  */
  public function normalizePhone(string $phone): string {
    $result = '';
    for ($i = 0; $i < strlen($phone); $i++) {
      $char = $phone[$i];
      if (is_numeric($char)) {
        $result .= $char;
      }
    }
    return $result;
  }
}