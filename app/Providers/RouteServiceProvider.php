<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Namespace for API controller
     *
     * @var string
    */
    protected $namespaceApi = 'App\Http\Controllers\API';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapBotRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
      Route::group([
        'prefix' => 'api',
        'as' => 'api.',
        'middleware' => 'api',
        'namespace' => $this->namespaceApi
      ], function (Router $router) {
        // Register categories routes
        $router->group([
          'prefix' => 'categories',
          'namespace' => 'Common'
        ], base_path('routes/api/categories.php'));

        // Register info routes
        $router->group([
          'namespace' => 'Info'
        ], base_path('routes/api/info.php'));

        // Register location routes
        $router->group([
          'namespace' => 'Common'
        ], base_path('routes/api/location.php'));

        // Authentication routes
        $router->group([], base_path('routes/api/authentication.php'));

        // Register user routes
        $this->registerUserRoutes($router);

        // Messenger routes
        $router->group([
          'as' => 'chats.',
          'prefix' => '/chats',
          'middleware' => 'auth:api',
          'namespace' => 'User',
        ], base_path('routes/api/messenger.php'));

        // Profiles routes
        $router->group([
          'as' => 'profiles.',
          'prefix' => '/profiles',
          'namespace' => 'User\\Profile'
        ], base_path('routes/api/profiles.php'));

        // Common routes
        $router->group([
          'namespace' => 'Common'
        ], base_path('routes/api/common.php'));
      });
    }

    protected function registerUserRoutes(Router $router) {
      // User routes
      $router->group([
        'middleware' => 'auth:api',
        'as' => 'user.',
        'prefix' => '/user',
        'namespace' => 'User'
      ], function () {
        // Common user routes
        Route::group([], base_path('routes/api/user/common.php'));

        // Profile user routes
        Route::group([
          'prefix' => '/profile',
          'as' => 'profile.',
          'namespace' => 'Profile',
        ], base_path('routes/api/user/profile.php'));

        // Specialities routes
        Route::group([
          'prefix' => '/profile/specialities',
          'as' => 'profile.specialities.',
          'namespace' => 'Profile',
        ], base_path('routes/api/user/specialities.php'));

        // Favourites section
        Route::group([
          'prefix' => '/favourites',
          'as' => 'fav.'
        ], base_path('routes/api/user/favourites.php'));

        // Cards section
        Route::group([
          'prefix' => '/cards',
          'as' => 'cards.'
        ], base_path('routes/api/user/cards.php'));

        // Notifications section
        Route::group([
          'prefix' => '/notifications',
          'as' => 'notifications.'
        ], base_path('routes/api/user/notifications.php'));
      });
    }

    /**
     * Define the routes for bots
     *
     * @return void
    */
    protected function mapBotRoutes() {
      Route::prefix('bot')
        ->as('bot.')
        ->middleware('bot')
        ->namespace($this->namespace)
        ->group(base_path('routes/bot.php'));
    }
}
