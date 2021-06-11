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

Route::get('/', 'ProfileSearchController@search')
  ->name('search');
Route::get('/random', 'ProfileSearchController@getRandom')
  ->name('random');
Route::get('/{id}', 'ProfileController@getById')
  ->where(['id' => REGEX_ID])
  ->name('id');
Route::post('/{profile}/views', 'ViewsController@add')
  ->where(['profile' => REGEX_ID])
  ->name('views.create');

// Complaints
Route::post('/{profile}/complaints', 'ProfileController@createComplaint')
  ->name('complaints.create');

Route::group([
  'prefix' => '/{profile}/reviews',
  'as' => 'reviews.',
  'where' => ['profile' => REGEX_ID]
], function (Illuminate\Routing\Router $router) {
  $router->get('/', 'ReviewsController@getById')
    ->name('get');
  $router->post('/', 'ReviewsController@create')
    ->name('create')
    ->middleware('auth:api');
  $router->get('/count', 'ReviewsController@countBySpecialities')
    ->name('count');
  $router->post('/{review}', 'ReviewsController@reply')
    ->where('review', REGEX_ID)
    ->name('reply');
  $router->delete('/', 'ReviewsController@delete')
    ->name('delete');
  // Complaints
  $router->post('/{review}/complaints', 'ReviewsController@createComplaint')
    ->where('review', REGEX_ID)
    ->name('complaints.create');
});
