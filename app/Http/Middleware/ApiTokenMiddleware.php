<?php

namespace App\Http\Middleware;

use Closure;

class ApiTokenMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param \Illuminate\Http\Request $request
   * @param \Closure $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    if (!config('auth.api.public')) {
      if ($request->header('API-TOKEN') != config('auth.api.token')) {
          return response()->json(['error' => 'API token is not provided or is invalid'], 401);
      }
    }
    return $next($request);
  }
}
