<?php


namespace App\Helpers;

use Illuminate\Support\Str;

/**
 * Abstract class with sole purpose of providing the needed functions for verification helpers
*/
abstract class VerificationHelperBase
{
  // Flags for usage
  const NOTHING_ON_SUCCESS = 0;
  const SAVE_ON_SUCCESS = 1;
  const DELETE_ON_SUCCESS = 2;

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
   * Creates the instance of helper
   *
   * @param bool $verificationEnabled
   * @param bool $isNexmoEnabled
  */
  public function __construct(bool $verificationEnabled, bool $isNexmoEnabled)
  {
    $this->verificationEnabled = $verificationEnabled;
    $this->isNexmoEnabled = $isNexmoEnabled;
  }

  /**
   * Function for generating UUID
   *
   * @return string
  */
  protected function generateUUID(): string {
    return Str::uuid();
  }

  /**
   * Function for generating the code
   *
   * @return string
  */
  protected function generateCode(): string {
    return (string)rand(100000, 999999);
  }

  /**
   * Public function for checking the code
   *
   * @param string $uuid
   * @param string $code
   * @param int $successFlag
   *
   * @return array|bool
  */
  abstract public function checkCode(string $uuid, string $code, int $successFlag = self::NOTHING_ON_SUCCESS);

  /**
   * Public function for checking UUID
   *
   * @param string $uuid
   *
   * @return mixed
  */
  abstract public function checkUUID(string $uuid);
}