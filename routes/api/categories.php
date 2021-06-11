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
// Get list of all categories
Route::get('/', 'CategoriesController@index')
  ->name('categories.all');

Route::get('/search', 'SearchController@searchCategories')
  ->name('categories.search');

// Get category information by id
Route::get('/{id}', 'CategoriesController@byId')
  ->where('id', REGEX_ID)
  ->name('categories.id');

// Get category information
Route::get('/{slug}', 'CategoriesController@bySlug')
  ->name('categories.slug');

