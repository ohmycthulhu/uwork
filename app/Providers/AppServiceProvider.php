<?php

namespace App\Providers;

use App\Helpers\MediaHelper;
use App\Helpers\NotificationHelper;
use App\Helpers\PaymentHelper;
use App\Helpers\SearchHelper;
use App\Models\Categories\Category;
use App\Models\Info\HelpCategory;
use App\Models\Info\HelpItem;
use App\Models\Media\Image;
use App\Models\Search\SearchHistory;
use App\Models\Transactions\Transaction;
use App\Models\User;
use App\Models\User\Profile;
use App\Models\User\ProfileSpeciality;
use App\Observers\ProfileObserver;
use App\Observers\ProfileSpecialityObserver;
use App\Observers\SlugableObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    Schema::defaultStringLength(191);
    //
    $this->app->bind('search-helper', function () {
      return new SearchHelper(new SearchHistory);
    });

    $this->app->bind('payment-helper', function () {
      return new PaymentHelper(new Transaction);
    });

    $this->app->bind('media-facade', function () {
      return new MediaHelper(new Image, 'public', 'default');
    });

    $this->app->bind('notifications-helper', function () {
      return new NotificationHelper(new User\Notification);
    });
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    User::observe(UserObserver::class);
    Profile::observe(ProfileObserver::class);
    Category::observe(SlugableObserver::class);
    HelpCategory::observe(SlugableObserver::class);
    HelpItem::observe(SlugableObserver::class);
    ProfileSpeciality::observe(ProfileSpecialityObserver::class);
  }
}
