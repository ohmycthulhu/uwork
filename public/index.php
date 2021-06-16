<?php

function getTime() {
  return time() + microtime(true);
}

$times = [
  'start' => getTime(),
];

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';

$times['autoload'] = getTime();

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$times['app'] = getTime();

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$times['kernel'] = getTime();

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$times['response'] = getTime();

$response->send();

$times['sending'] = getTime();

//$kernel->terminate($request, $response);

$times['terminating'] = getTime();
$keys = array_keys($times);

$diffs = array_reduce(
  array_keys($keys),
  function ($arr, $index) use ($times, $keys) {
    if ($index === 0) {
      $val = 0;
    } else {
      $val = $times[$keys[$index]] - $times[$keys[$index - 1]];
    }
    $arr[$keys[$index]] = $val;
    return $arr;
  },
  []
);

if (false) {
  echo "<br>";
  foreach ($diffs as $type => $time) {
    echo "$type - $time<br>";
  }
}
