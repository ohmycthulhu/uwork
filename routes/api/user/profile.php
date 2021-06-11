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
Route::post('/', 'ProfileController@create')
  ->name('create');
Route::get('/', 'ProfileController@get')
  ->name('get');
Route::post('/update', 'ProfileController@update')
  ->name('update');
Route::put('/images/{imageId}', 'ProfileController@setImageSpeciality')
  ->name('images.update');

Route::get('/reviews', 'ReviewsController@get')
  ->name('reviews.get');
