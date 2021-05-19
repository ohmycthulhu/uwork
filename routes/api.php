<?php

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
    ->name('categories.all');

  $router->get('/search', 'API\\SearchController@searchCategories')
    ->name('categories.search');

  // Get category information
  $router->get('/{slug}', 'API\\CategoriesController@bySlug')
    ->name('categories.slug');
});

/**
 * Location routes
 */
// Regions information
Route::group([
  'prefix' => 'regions'
], function ($router) {
  $router->get('/', 'API\\LocationController@regions')
    ->name('regions.all');
  $router->get('/{id}', 'API\\LocationController@regionById')
    ->name('regions.id');
  $router->get('/{id}/cities', 'API\\LocationController@regionCities')
    ->name('regions.id.cities');
});

// Cities information
Route::get('/cities/{id}', 'API\\LocationController@cityById')
  ->name('cities.id');
Route::get('/cities/{id}/districts', 'API\\LocationController@cityDistricts')
  ->name('cities.id.districts');

/*
 * Authentication methods
 */
// Route to set phone number
Route::post('/phones', 'API\\AuthenticationController@promptPhone')
  ->name('phones');
Route::post('/register', 'API\\AuthenticationController@register')
  ->name('register');
Route::post('/verify/{uuid}', 'API\\AuthenticationController@verifyPhoneNumber')
  ->name('verify');
//Route::post('/resend/{phone}', 'API\\AuthenticationController@resendVerification')
//  ->name('resend');
Route::post('/login', 'API\\AuthenticationController@login')
  ->name('login');
Route::post('/passwords', 'API\\AuthenticationController@resetPassword')
  ->name('reset');
Route::post('/passwords/{uuid}', 'API\\AuthenticationController@setPassword')
  ->name('reset.set');

/*
 * Authenticated routes
 */
Route::group([
  'middleware' => 'auth:api',
  'as' => 'user.',
  'prefix' => '/user',
], function (Illuminate\Routing\Router $router) {
  $router->get('/', 'API\\AuthenticationController@user')
    ->name('get');
  $router->delete('/', 'API\\UserController@delete')
    ->name('delete');

  $router->match(['put', 'post'], '/', 'API\\UserController@changeProfile')
    ->name('update.profile');
  $router->delete('/avatar', 'API\\UserController@removeAvatar')
    ->name('delete.avatar');
  // Note: Deprecated Route
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

      // Get grouped specialities
      $router->get('/categories', 'API\\Profile\\SpecialitiesController@getCategories');

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
    'prefix' => '/favourites',
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

  // Notifications
  $router->group([
    'prefix' => '/notifications',
    'as' => 'notifications.'
  ], function (\Illuminate\Routing\Router $router) {
    $router->get('/', 'NotificationsController@get')
      ->name('get');
    $router->match(['post', 'put'], '/', 'NotificationsController@markRead')
      ->name('read');
  });
});

// Messenger
Route::group([
  'as' => 'chats.',
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
  'as' => 'profiles.',
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

  // Complaints
  $router->post('/{profile}/complaints', 'API\\ProfileController@createComplaint')
    ->name('complaints.create');

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
    // Complaints
    $router->post('/reviews/{review}/complaints', 'API\\Profile\\ReviewsController@createComplaint')
      ->name('complaints.create');
  });
});

Route::get('autocomplete', 'API\\SearchController@getAutocomplete')
  ->name('autocomplete');

/*
 * File routes
 */
Route::post('/files', 'API\\FileController@uploadImage')
  ->name('files');

/*
 * Info routes
 */
Route::get('/info', 'API\\InfoController@index')
  ->name('info');
Route::get('/info/about', 'API\\InfoController@about')
  ->name('info.about');
Route::get('/info/faq', 'API\\InfoController@faq')
  ->name('info.faq');

/**
 * Routes for upvoting and downvoting info parts
*/
Route::group([
  'prefix' => '/info/{type}',
  'as' => 'texts.',
  'where' => ['type' => implode('|', config('info.texts'))]
  ], function (\Illuminate\Routing\Router $router) {
  $router->get('/', 'API\\TextStatisticsController@getText')
    ->name('get');
  $router->match(['post', 'put'], '/statistics', 'API\\TextStatisticsController@addUpvote')
    ->name('upvote');
  $router->delete('/statistics', 'API\\TextStatisticsController@addDownvote')
    ->name('downvote');
});

// Help categories
Route::get('/help-categories', 'API\\InfoController@getHelpCategories')
  ->name('helpCategories.all');
Route::get('/help-categories/{slug}', 'API\\InfoController@getHelpCategory')
  ->name('helpCategories.slug');
Route::get('/help-items/{slug}', 'API\\InfoController@getHelpItem')
  ->name('helpItems.slug');

// Communication routes
Route::get('/appeal-reasons', 'API\\CommunicationController@appealReasons')
  ->name('appealReasons');
Route::post('/appeals', 'API\\CommunicationController@create')
  ->name('appeals.create');

// Complaints
Route::get('/complaint-types', function () {
  return response()->json([
    'types' => \App\Models\Complaints\ComplaintType::all()
  ]);
});

// Login tokens
Route::post('/tokens/{uuid}', 'API\\BotController@verifyToken')
  ->name('tokens.verify');
