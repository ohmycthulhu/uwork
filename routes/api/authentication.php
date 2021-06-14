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
 * Authentication methods
 */
// Route to set phone number
Route::post('/phones', 'User\\AuthenticationController@promptPhone')
  ->name('phones');
Route::post('/register', 'User\\AuthenticationController@register')
  ->name('register');
Route::post('/verify/{uuid}', 'User\\AuthenticationController@verifyPhoneNumber')
  ->name('verify');

Route::post('/login', 'User\\AuthenticationController@login')
  ->name('login');
Route::post('/logout', 'User\\AuthenticationController@logout')
  ->name('logout')
  ->middleware('auth:api');
Route::post('/refresh', 'User\\AuthenticationController@refreshToken')
  ->name('refresh')
  ->middleware('auth:api');
Route::post('/passwords', 'User\\AuthenticationController@resetPassword')
  ->name('reset');
Route::post('/passwords/verify', 'User\\AuthenticationController@verifyPasswordReset')
  ->name('reset.verify');
Route::post('/passwords/{uuid}', 'User\\AuthenticationController@setPassword')
  ->name('reset.set');

// Login tokens
Route::post('/tokens/{uuid}', 'Common\\BotController@verifyToken')
  ->name('tokens.verify');
