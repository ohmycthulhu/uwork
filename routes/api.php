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
