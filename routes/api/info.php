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
 * Info routes
 */
Route::get('/info', 'InfoController@index')
  ->name('info');
Route::get('/info/about', 'InfoController@about')
  ->name('info.about');
Route::get('/info/faq', 'InfoController@faq')
  ->name('info.faq');

/**
 * Routes for upvoting and downvoting info parts
*/
Route::group([
  'prefix' => '/info/{type}',
  'as' => 'texts.',
  'where' => ['type' => implode('|', config('info.texts'))]
  ], function (\Illuminate\Routing\Router $router) {
  $router->get('/', 'TextStatisticsController@getText')
    ->name('get');
  $router->match(['post', 'put'], '/statistics', 'TextStatisticsController@addUpvote')
    ->name('upvote');
  $router->delete('/statistics', 'TextStatisticsController@addDownvote')
    ->name('downvote');
});

// Help categories
Route::get('/help-categories', 'InfoController@getHelpCategories')
  ->name('helpCategories.all');
Route::get('/help-categories/{slug}', 'InfoController@getHelpCategory')
  ->name('helpCategories.slug');
Route::get('/help-items/{slug}', 'InfoController@getHelpItem')
  ->name('helpItems.slug');

// Communication routes
Route::get('/appeal-reasons', 'CommunicationController@appealReasons')
  ->name('appealReasons');
Route::post('/appeals', 'CommunicationController@create')
  ->name('appeals.create');

// Complaints
Route::get('/complaint-types', 'ComplaintTypesController@index');
