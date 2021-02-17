<?php

namespace App\Providers;

use App\Models\Categories\Category;
use App\Models\User\ProfileSpeciality;
use App\Observers\ProfileSpecialityObserver;
use App\Observers\SlugableObserver;
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
        //
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
