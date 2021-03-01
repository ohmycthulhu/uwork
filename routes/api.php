<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});

/**
 * Categories routes
 */
Route::group([
  'prefix' => 'categories'
], function ($router) {
  // Get list of all categories
  $router->get('/', 'API\\CategoriesController@index')
    ->name('api.categories.all');

  // Get category information
  $router->get('/{slug}', 'API\\CategoriesController@bySlug')
    ->name('api.categories.slug');
});

/**
 * Location routes
 */
// Regions information
Route::group([
  'prefix' => 'regions'
], function ($router) {
  $router->get('/', 'API\\LocationController@regions')
    ->name('api.regions.all');
  $router->get('/{id}', 'API\\LocationController@regionById')
    ->name('api.regions.id');
  $router->get('/{id}/cities', 'API\\LocationController@regionCities')
    ->name('api.regions.id.cities');
});

// Cities information
Route::get('/cities/{id}', 'API\\LocationController@cityById')
  ->name('api.cities.id');
Route::get('/cities/{id}/districts', 'API\\LocationController@cityDistricts')
  ->name('api.cities.id.districts');

/*
 * Authentication methods
 */
Route::post('/register', 'API\\AuthenticationController@register')
  ->name('api.register');
Route::post('/verify/{uuid}', 'API\\AuthenticationController@verifyPhoneNumber')
  ->name('api.verify');
Route::post('/resend/{phone}', 'API\\AuthenticationController@resendVerification')
  ->name('api.resend');
Route::post('/login', 'API\\AuthenticationController@login')
  ->name('api.login');
Route::post('/passwords', 'API\\AuthenticationController@resetPassword')
  ->name('api.reset');
Route::post('/passwords/{uuid}', 'API\\AuthenticationController@setPassword')
  ->name('api.reset.set');

/*
 * Authenticated routes
 */
Route::group([
  'middleware' => 'auth:api'
], function ($router) {
  $router->group([
    'prefix' => '/user'
  ], function ($router) {
    $router->get('/', 'API\\AuthenticationController@user')->name('api.user');

    $router->put('/', 'API\\UserController@changeProfile')->name('api.user.update.profile');
    $router->put('/emails', 'API\\UserController@changeEmail')->name('api.user.update.email');
    $router->put('/phones', 'API\\UserController@changePhone')->name('api.user.update.phone');
    $router->put('/passwords', 'API\\UserController@changePassword')->name('api.user.update.password');

    $router->group([
      'prefix' => '/profile',
      'as' => 'api.profile.'
    ], function ($router) {
      $router->post('/', 'API\\ProfileController@create')->name('create');
      $router->get('/', 'API\\ProfileController@get')->name('get');
      $router->post('/update', 'API\\ProfileController@update')->name('update');

      $router->get('/reviews', 'API\\ProfileController@getReviews')->name('reviews.get');
    });
  });
});

Route::group([
  'as' => 'api.profiles.',
  'prefix' => '/profiles'
], function ($router) {
  $router->get('/{id}', 'API\\ProfileController@getById')
    ->name('id');
  $router->post('/{profile}/views', 'API\\ProfileController@addView')
    ->name('views.create');

  $router->group([
    'prefix' => '/profiles',
    'as' => 'reviews.'
  ], function ($router) {
    $router->post('/{profile}/reviews', 'API\\ProfileController@createReview')
      ->name('create');
    $router->delete('/{profile}/reviews', 'API\\ProfileController@deleteReview')
      ->name('delete');
  });
});

Route::group([
  'prefix' => 'profiles'
], function (\Illuminate\Routing\Router $router) {
  $router->get('/', 'API\\SearchController@search')
    ->name('api.profiles.search');
});

Route::get('autocomplete', 'API\\SearchController@getAutocomplete')
  ->name('api.autocomplete');


/*
 * File routes
 */
Route::post('/files', 'API\\FileController@uploadImage')
  ->name('api.files');