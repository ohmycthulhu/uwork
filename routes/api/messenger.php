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

// Messenger
Route::get('/', 'MessengerController@getChats')
  ->name('list');

Route::get('/{user}', 'MessengerController@getMessages')
  ->where(['user' => REGEX_ID])
  ->name('get');

Route::put('/{user}', 'MessengerController@markRead')
  ->where(['user' => REGEX_ID])
  ->name('read');

Route::post('/{user}', 'MessengerController@sendMessage')
  ->where(['user' => REGEX_ID])
  ->name('create');

Route::delete('/{user}', 'MessengerController@deleteChat')
  ->where(['user' => REGEX_ID])
  ->name('delete');

Route::get('/{user}/search', 'MessengerController@search')
  ->where(['user' => REGEX_ID])
  ->name('search');
