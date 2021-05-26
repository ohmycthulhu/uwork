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

if (!defined('REGEX_ID')) {
  define("REGEX_ID", '[0-9]+');
}

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

  // Get category information by id
  $router->get('/{id}', 'API\\CategoriesController@byId')
    ->where('id', REGEX_ID)
    ->name('categories.id');

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
    ->where('id', REGEX_ID)
    ->name('regions.id');
  $router->get('/{id}/cities', 'API\\LocationController@regionCities')
    ->where('id', REGEX_ID)
    ->name('regions.id.cities');
});

// Cities information
Route::get('/cities/{id}', 'API\\LocationController@cityById')
  ->name('cities.id');
Route::get('/cities/{id}/districts', 'API\\LocationController@cityDistricts')
  ->where('id', REGEX_ID)
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
      $router->get('/categories', 'API\\Profile\\SpecialitiesController@getCategories')
        ->name('categories');

      // Search the category
      $router->get('/categories/search', 'API\\Profile\\SpecialitiesController@searchCategories')
        ->name('categories.search');

      // Get grouped specialities
      $router->get('/categories/{categoryId}', 'API\\Profile\\SpecialitiesController@getSubcategories')
        ->where('categoryId', REGEX_ID)
        ->name('categories.id');

      $router->post('/categories/{category}', 'API\\Profile\\SpecialitiesController@createMultiple')
        ->where('category', REGEX_ID)
        ->name('categories.add');

      $router->delete('/categories/{category}', 'API\\Profile\\SpecialitiesController@deleteMultiple')
        ->where('category', REGEX_ID)
        ->name('categories.remove');

      $router->get('{category}', 'API\\Profile\\SpecialitiesController@getByCategory')
        ->where('category', REGEX_ID)
        ->name('getByCategory');

      // Update specialities
      $router->match(['put', 'post'], '/{specialityId}', 'API\\Profile\\SpecialitiesController@update')
        ->where('specialityId', REGEX_ID)
        ->name('update');

      // Delete specialities
      $router->delete('/{specialityId}', 'API\\Profile\\SpecialitiesController@delete')
        ->where('specialityId', REGEX_ID)
        ->name('delete');

      // Upload image to speciality
      $router->post('/{specialityId}/images', 'API\\Profile\\SpecialitiesController@uploadImage')
        ->where('specialityId', REGEX_ID)
        ->name('images.upload');

      // Remove image
      $router->delete('/{specialityId}/images/{imageId}', 'API\\Profile\\SpecialitiesController@removeImage')
        ->where(['specialityId' => REGEX_ID, 'imageId' => REGEX_ID])
        ->name('images.delete');

      // Reorder image
      $router->put('/{specialityId}/images/{imageId}', 'API\\Profile\\SpecialitiesController@updateImage')
        ->where(['specialityId' => REGEX_ID, 'imageId' => REGEX_ID])
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
      ->where(['serviceId' => REGEX_ID])
      ->name('add');

    // Route to remove service from services
    $router->delete('/{serviceId}', 'API\\FavouritesController@remove')
      ->where(['serviceId' => REGEX_ID])
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
      ->where(['cardId' => REGEX_ID])
      ->name('update');

    // Route to delete
    $router->delete('/{cardId}', 'API\\CardsController@delete')
      ->where(['cardId' => REGEX_ID])
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
    ->where(['user' => REGEX_ID])
    ->name('get');

  $router->put('/{user}', 'API\\MessengerController@markRead')
    ->where(['user' => REGEX_ID])
    ->name('read');

  $router->post('/{user}', 'API\\MessengerController@sendMessage')
    ->where(['user' => REGEX_ID])
    ->name('create');

  $router->delete('/{user}', 'API\\MessengerController@deleteChat')
    ->where(['user' => REGEX_ID])
    ->name('delete');

  $router->get('/{user}/search', 'API\\MessengerController@search')
    ->where(['user' => REGEX_ID])
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
    ->where(['id' => REGEX_ID])
    ->name('id');
  $router->post('/{profile}/views', 'API\\Profile\\ViewsController@add')
    ->where(['profile' => REGEX_ID])
    ->name('views.create');

  // Complaints
  $router->post('/{profile}/complaints', 'API\\ProfileController@createComplaint')
    ->name('complaints.create');

  $router->group([
    'prefix' => '/{profile}',
    'as' => 'reviews.',
    'where' => ['profile' => REGEX_ID]
  ], function (Illuminate\Routing\Router $router) {
    $router->get('/reviews', 'API\\Profile\\ReviewsController@getById')
      ->name('get');
    $router->get('/reviews/count', 'API\\Profile\\ReviewsController@countBySpecialities')
      ->name('count');
    $router->post('/reviews', 'API\\Profile\\ReviewsController@create')
      ->name('create')
      ->middleware('auth:api');
    $router->post('/reviews/{review}', 'API\\Profile\\ReviewsController@reply')
      ->where('review', REGEX_ID)
      ->name('reply');
    $router->delete('/reviews', 'API\\Profile\\ReviewsController@delete')
      ->name('delete');
    // Complaints
    $router->post('/reviews/{review}/complaints', 'API\\Profile\\ReviewsController@createComplaint')
      ->where('review', REGEX_ID)
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
