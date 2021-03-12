<?php


namespace App\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Facade for working with Payment helper
 *
 * @method static array initialize(float $price)
 * @method static bool  cancel(string $id)
 * @method static bool  confirm(string $uuid)
 *
 * @const string CONS
*/
class PaymentFacade extends Facade
{
  protected static function getFacadeAccessor(): string
  {
    return 'payment-helper';
  }
}