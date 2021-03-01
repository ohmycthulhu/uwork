<?php

namespace App\Providers;

use App\Helpers\SearchHelper;
use App\Models\Categories\Category;
use App\Models\Search\SearchHistory;
use App\Models\User\ProfileSpeciality;
use App\Observers\ProfileSpecialityObserver;
use App\Observers\SlugableObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Category::observe(SlugableObserver::class);
        ProfileSpeciality::observe(ProfileSpecialityObserver::class);
    }
}
