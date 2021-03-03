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
], function (Illuminate\Routing\Router $router) {
  $router->group([
    'prefix' => '/user'
  ], function (Illuminate\Routing\Router $router) {
    $router->get('/', 'API\\AuthenticationController@user')
      ->name('api.user');

    $router->put('/', 'API\\UserController@changeProfile')
      ->name('api.user.update.profile');
    $router->put('/emails', 'API\\UserController@changeEmail')
      ->name('api.user.update.email');
    $router->put('/phones', 'API\\UserController@changePhone')
      ->name('api.user.update.phone');
    $router->put('/passwords', 'API\\UserController@changePassword')
      ->name('api.user.update.password');

    $router->put('/settings', 'API\\UserController@updateSettings')
      ->name('api.user.settings');

    // Profiles section
    $router->group([
      'prefix' => '/profile',
      'as' => 'api.profile.'
    ], function (Illuminate\Routing\Router $router) {
      $router->post('/', 'API\\ProfileController@create')
        ->name('create');
      $router->get('/', 'API\\ProfileController@get')
        ->name('get');
      $router->post('/update', 'API\\ProfileController@update')
        ->name('update');

      $router->get('/reviews', 'API\\Profile\\ReviewsController@get')
        ->name('reviews.get');

      $router->group([
        'prefix' => '/specialities',
        'as' => 'specialities.'
      ], function (Illuminate\Routing\Router $router) {
        // Create specialities
        $router->post('/', 'API\\Profile\\SpecialitiesController@create')
          ->name('create');

        // Get specialities
        $router->get('/', 'API\\Profile\\SpecialitiesController@get')
          ->name('list');

        // Update specialities
        $router->put('/{specialityId}', 'API\\Profile\\SpecialitiesController@update')
          ->name('update');

        // Delete specialities
        $router->delete('/{specialityId}', 'API\\Profile\\SpecialitiesController@delete')
          ->name('delete');
      });
    });

    // Favourites section
    $router->group([
      'prefix' =>'/favourites',
      'as' => 'api.fav.'
    ], function (Illuminate\Routing\Router $router) {
      // Route to get list of favourites
      $router->get('/', 'API\\FavouritesController@get')
        ->name('list');

      // Route to add service as favourite
      $router->post('/{serviceId}', 'API\\FavouritesController@add')
        ->name('add');

      // Route to remove service from services
      $router->delete('/{serviceId}', 'API\\FavouritesController@remove')
        ->name('remove');
    });

    // Cards section
    $router->group([
      'prefix' => 'cards',
      'as' => 'api.user.cards.'
    ], function (Illuminate\Routing\Router $router) {
      // Route to create
      $router->post('/', 'API\\CardsController@create')
        ->name('create');

      // Route to get
      $router->get('/', 'API\\CardsController@get')
        ->name('list');

      // Route to update
      $router->put('/{cardId}', 'API\\CardsController@update')
        ->name('update');

      // Route to delete
      $router->delete('/{cardId}', 'API\\CardsController@delete')
        ->name('delete');
    });
  });
});

// Messenger
Route::group([
  'as' => 'api.chats.',
  'prefix' => '/chats',
  'middleware' => 'auth:api',
], function (\Illuminate\Routing\Router $router) {
  $router->get('/', 'API\\MessengerController@getChats')
    ->name('list');

  $router->get('/{user}', 'API\\MessengerController@getMessages')
    ->name('get');

  $router->post('/{user}', 'API\\MessengerController@sendMessage')
    ->name('create');

  $router->delete('/{user}', 'API\\MessengerController@deleteChat')
    ->name('delete');

  $router->get('/{user}/search', 'API\\MessengerController@search')
    ->name('search');
});

// Other profiles
Route::group([
  'as' => 'api.profiles.',
  'prefix' => '/profiles'
], function (Illuminate\Routing\Router $router) {
  $router->get('/', 'API\\SearchController@search')
    ->name('search');
  $router->get('/{id}', 'API\\ProfileController@getById')
    ->name('id');
  $router->post('/{profile}/views', 'API\\Profile\\ViewsController@add')
    ->name('views.create');

  $router->group([
    'prefix' => '/profiles',
    'as' => 'reviews.'
  ], function (Illuminate\Routing\Router $router) {
    $router->get('/{profile}/reviews', 'API\\Profile\\ReviewsController@getById')
      ->name('create');
    $router->post('/{profile}/reviews', 'API\\Profile\\ReviewsController@create')
      ->name('create');
    $router->delete('/{profile}/reviews', 'API\\Profile\\ReviewsController@delete')
      ->name('delete');
  });
});

Route::get('autocomplete', 'API\\SearchController@getAutocomplete')
  ->name('api.autocomplete');

/*
 * File routes
 */
Route::post('/files', 'API\\FileController@uploadImage')
  ->name('api.files');