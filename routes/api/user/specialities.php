<?php

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

use Illuminate\Support\Facades\Route;


if (!defined('REGEX_ID')) {
  define("REGEX_ID", '[0-9]+');
}

// Create specialities
Route::post('/', 'SpecialitiesController@create')
  ->name('create');

// Get specialities
Route::get('/', 'SpecialitiesController@get')
  ->name('list');

Route::post('/categories/{category}', 'SpecialitiesController@createMultiple')
  ->where('category', REGEX_ID)
  ->name('categories.add');

Route::delete('/categories/{category}', 'SpecialitiesController@deleteMultiple')
  ->where('category', REGEX_ID)
  ->name('categories.remove');

Route::get('{category}', 'SpecialitiesController@getByCategory')
  ->where('category', REGEX_ID)
  ->name('getByCategory');

// Update specialities
Route::match(['put', 'post'], '/{specialityId}', 'SpecialitiesController@update')
  ->where('specialityId', REGEX_ID)
  ->name('update');

// Delete specialities
Route::delete('/{specialityId}', 'SpecialitiesController@delete')
  ->where('specialityId', REGEX_ID)
  ->name('delete');

Route::group([
  'prefix' => '/categories',
  'as' => 'categories'
], function (\Illuminate\Routing\Router $router) {
  // Get grouped specialities
  $router->get('/', 'SpecialityCategoriesController@getCategories')
    ->name('');

// Search the category
  $router->get('/search', 'SpecialityCategoriesController@searchCategories')
    ->name('.search');

// Get grouped specialities
  $router->get('/{categoryId}', 'SpecialityCategoriesController@getSubcategories')
    ->where('categoryId', REGEX_ID)
    ->name('.id');
});

// Image routes
Route::group([
  'prefix' => '/{specialityId}/images',
  'where' => ['specialityId' => REGEX_ID],
  'as' => 'images.'
], function (\Illuminate\Routing\Router $router) {
// Upload image to speciality
  $router->post('/', 'SpecialitiesController@uploadImage')
    ->name('upload');

// Remove image
  $router->delete('/{imageId}', 'SpecialitiesController@removeImage')
    ->where(['imageId' => REGEX_ID])
    ->name('delete');

// Reorder image
  $router->put('/{imageId}', 'SpecialitiesController@updateImage')
    ->where(['imageId' => REGEX_ID])
    ->name('update');
});