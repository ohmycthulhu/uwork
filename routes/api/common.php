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

Route::get('autocomplete', 'SearchController@getAutocomplete')
  ->name('autocomplete');

/*
 * File routes
 */
Route::post('/images', 'FileController@uploadImage')
  ->name('images');
