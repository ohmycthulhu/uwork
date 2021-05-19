<?php

namespace App\Http\Middleware\API;

use App\Models\Authentication\Bot;
use Closure;

class BotMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $token = $request->header('API-TOKEN');
      $bot = $token ? Bot::query()->token($token)->state(true)->first() : null;
      if (!$bot) {
        return response()->json([
          'error' => 'API token is provided or not valid',
          'status' => 'error',
        ], 403);
      }
        return $next($request);
    }
}
