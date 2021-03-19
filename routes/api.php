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

  $router->get('/search', 'API\\SearchController@searchCategories')
    ->name('api.categories.search');

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
// Route to set phone number
Route::post('/phones', 'API\\AuthenticationController@promptPhone')
  ->name('api.phones');
Route::post('/register', 'API\\AuthenticationController@register')
  ->name('api.register');
Route::post('/verify/{uuid}', 'API\\AuthenticationController@verifyPhoneNumber')
  ->name('api.verify');
//Route::post('/resend/{phone}', 'API\\AuthenticationController@resendVerification')
//  ->name('api.resend');
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
  'middleware' => 'auth:api',
  'as' => 'api.user.',
], function (Illuminate\Routing\Router $router) {
  $router->group([
    'prefix' => '/user'
  ], function (Illuminate\Routing\Router $router) {
    $router->get('/', 'API\\AuthenticationController@user')
      ->name('get');

    $router->match(['put', 'post'], '/', 'API\\UserController@changeProfile')
      ->name('update.profile');
    $router->put('/emails', 'API\\UserController@changeEmail')
      ->name('update.email');
    $router->put('/phones', 'API\\UserController@changePhone')
      ->name('update.phone');
    $router->put('/passwords', 'API\\UserController@changePassword')
      ->name('update.password');

    $router->put('/settings', 'API\\UserController@updateSettings')
      ->name('settings');

    // Profiles section
    $router->group([
      'prefix' => '/profile',
      'as' => 'profile.'
    ], function (Illuminate\Routing\Router $router) {
      $router->post('/', 'API\\ProfileController@create')
        ->name('create');
      $router->get('/', 'API\\ProfileController@get')
        ->name('get');
      $router->post('/update', 'API\\ProfileController@update')
        ->name('update');
      $router->put('/images/{imageId}', 'API\\ProfileController@setImageSpeciality')
        ->name('images.update');

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

        // Upload image to speciality
        $router->post('/{specialityId}/images', 'API\\Profile\\SpecialitiesController@uploadImage')
          ->name('images.upload');

        // Remove image
        $router->delete('/{specialityId}/images/{imageId}', 'API\\Profile\\SpecialitiesController@removeImage')
          ->name('images.delete');

        // Reorder image
        $router->put('/{specialityId}/images/{imageId}', 'API\\Profile\\SpecialitiesController@updateImage')
          ->name('images.update');
      });
    });

    // Favourites section
    $router->group([
      'prefix' =>'/favourites',
      'as' => 'fav.'
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
      'as' => 'cards.'
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

  $router->put('/{user}', 'API\\MessengerController@markRead')
    ->name('read');

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
  $router->get('/random', 'API\\ProfileController@getRandom')
    ->name('random');
  $router->get('/{id}', 'API\\ProfileController@getById')
    ->name('id');
  $router->post('/{profile}/views', 'API\\Profile\\ViewsController@add')
    ->name('views.create');

  $router->group([
    'prefix' => '/{profile}',
    'as' => 'reviews.'
  ], function (Illuminate\Routing\Router $router) {
    $router->get('/reviews', 'API\\Profile\\ReviewsController@getById')
      ->name('get');
    $router->get('/reviews/count', 'API\\Profile\\ReviewsController@countBySpecialities')
      ->name('count');
    $router->post('/reviews', 'API\\Profile\\ReviewsController@create')
      ->name('create')
      ->middleware('auth:api');
    $router->post('/reviews/{review}', 'API\\Profile\\ReviewsController@reply')
      ->name('reply');
    $router->delete('/reviews', 'API\\Profile\\ReviewsController@delete')
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

/*
 * Info routes
 */
Route::get('/info', 'API\\InfoController@index')
  ->name('api.info');
Route::get('/info/about', 'API\\InfoController@about')
  ->name('api.info.about');
Route::get('/info/faq', 'API\\InfoController@faq')
  ->name('api.info.faq');

// Communication routes
Route::get('/appeal-reasons', 'API\\CommunicationController@appealReasons')
  ->name('api.appealReasons');
Route::post('/appeals', 'API\\CommunicationController@create')
  ->name('api.appeals.create');
