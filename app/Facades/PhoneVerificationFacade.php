<?php


namespace App\Facades;


use App\Models\User;
use Illuminate\Support\Facades\Facade;


/**
 * Facade to provide functionality to phone verification helper
 *
 * @method static string      createSession(?User $user, ?string $class, ?int $id, string $phone)
 * @method static bool        checkUUID(string $uuid)
 * @method static array|bool  checkCode(string $uuid, string $code, bool $deleteOnSuccess = null)
 * @method static string|null getVerifiedPhone(string $uuid)
 * @method static void        removeVerifiedPhone(string $uuid)
 * @method static bool        isBlocked(string $phone)
 * @method static void        blockPhone(string $phone)
 * @method static string      normalizePhone(string $phone)
 *
*/
class PhoneVerificationFacade extends Facade
{
  /**
   * Get registration name of component
   *
   * @return string
  */
  protected static function getFacadeAccessor(): string
  {
    return "phone-verification";
  }
}