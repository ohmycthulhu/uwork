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

/*
 * Authenticated routes
 */
Route::get('/', 'AuthenticationController@user')
  ->name('get');
Route::delete('/', 'UserController@delete')
  ->name('delete');

Route::match(['put', 'post'], '/', 'UserController@changeProfile')
  ->name('update.profile');
Route::delete('/avatar', 'UserController@removeAvatar')
  ->name('delete.avatar');
// Note: Deprecated Route
Route::put('/emails', 'UserController@changeEmail')
  ->name('update.email');
Route::put('/phones', 'UserController@changePhone')
  ->name('update.phone');
Route::put('/passwords', 'UserController@changePassword')
  ->name('update.password');

Route::put('/settings', 'UserController@updateSettings')
  ->name('settings');

