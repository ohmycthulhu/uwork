<?php

namespace App\Providers;

use App\Helpers\PhoneVerificationHelper;
use App\Helpers\ResetPasswordHelper;
use Illuminate\Support\ServiceProvider;

class VerificationServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    // Register phone verification facade
    $this->app->bind('phone-verification', function () {
      $isNexmoEnabled = !config('nexmo.is_disabled');
      return new PhoneVerificationHelper(config('app.code_chek_enabled'), $isNexmoEnabled);
    });

    // Register reset password facade
    $this->app->bind('reset-password', function () {
      $isNexmoEnabled = !config('nexmo.is_disabled');
      return new ResetPasswordHelper($isNexmoEnabled);
    });
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot()
  {
    //
  }
}
