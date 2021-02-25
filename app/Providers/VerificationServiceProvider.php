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
      return new PhoneVerificationHelper(config('app.code_chek_enabled'));
    });

    // Register reset password facade
    $this->app->bind('reset-password', function () {
      return new ResetPasswordHelper();
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
