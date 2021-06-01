<?php


namespace App\Facades;


use App\Helpers\VerificationHelperBase;
use App\Models\User;
use Illuminate\Support\Facades\Facade;

/**
 * Facade to provide functionality of reset password
 *
 * @method static string   createSession(User $user, bool $withEmail, bool $withPhone)
 * @method static int|null checkUUID(string $uuid)
 * @method static bool     checkCode(string $uuid, string $code, int $successFlag = VerificationHelperBase::NOTHING_ON_SUCCESS)
 * @method static string   removeUuid(string $uuid)
 *
*/
class ResetPasswordFacade extends Facade
{
  /**
   * Method to get facade accessor
   *
   * @return string
  */
  protected static function getFacadeAccessor(): string
  {
    return 'reset-password';
  }
}