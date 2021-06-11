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
// Route to create
Route::post('/', 'CardsController@create')
  ->name('create');

// Route to get
Route::get('/', 'CardsController@get')
  ->name('list');

// Route to update
Route::put('/{cardId}', 'CardsController@update')
  ->where(['cardId' => REGEX_ID])
  ->name('update');

// Route to delete
Route::delete('/{cardId}', 'CardsController@delete')
  ->where(['cardId' => REGEX_ID])
  ->name('delete');
