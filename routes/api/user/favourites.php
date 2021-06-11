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

// Route to get list of favourites
Route::get('/', 'FavouritesController@get')
  ->name('list');

// Route to add service as favourite
Route::post('/{serviceId}', 'FavouritesController@add')
  ->where(['serviceId' => REGEX_ID])
  ->name('add');

// Route to remove service from services
Route::delete('/{serviceId}', 'FavouritesController@remove')
  ->where(['serviceId' => REGEX_ID])
  ->name('remove');

