<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiTokenMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param Request $request
   * @param Closure $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    if (!config('auth.api.public')) {
      if ($request->header('API-TOKEN') != config('auth.api.token')) {
          return response()->json(['error' => 'API token is not provided or is invalid'], 401);
      }
    }
    return $next($request);
  }
}
