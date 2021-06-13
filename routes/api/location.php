<?php

use Illuminate\Support\Facades\Route;

/**
 * Location routes
 */
// Regions information
Route::group([
  'prefix' => 'regions'
], function ($router) {
  $router->get('/', 'LocationController@regions')
    ->name('regions.all');
  $router->get('/{id}', 'LocationController@regionById')
    ->where('id', REGEX_ID)
    ->name('regions.id');
  $router->get('/{id}/cities', 'LocationController@regionCities')
    ->where('id', REGEX_ID)
    ->name('regions.id.cities');
});

Route::group([
  'prefix' => 'cities/{id}',
  'as' => 'cities.',
  'where' => ['id' => REGEX_ID]
], function (\Illuminate\Routing\Router $router) {
// Cities information
  $router->get('/', 'LocationController@cityById')
    ->name('id');
  $router->get('/districts', 'LocationController@cityDistricts')
    ->name('id.districts');
  $router->get('/subways', 'LocationController@citySubways')
    ->name('id.subways');
});
