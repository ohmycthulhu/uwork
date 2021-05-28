<?php


namespace App\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Facade for using bot login helper
 *
 * @method static string      generateToken(string $phone)
 * @method static string|null verifyToken(string $token)
 *
*/
class BotLoginFacade extends Facade
{
  /**
   * Facade accessor
   *
   * @return string
  */
  protected static function getFacadeAccessor(): string
  {
    return "bot-login-facade";
  }
}