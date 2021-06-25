<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * Route for home page
     *
     * @return string
    */
    public function index(): string {
      return date('Y-m-d H:i:s');
    }

    /**
     * Throws an exception
     *
     * @throws \Exception
     *
     * @return JsonResponse
    */
    public function error(): JsonResponse {
      throw new \Exception("Exception!");
      return response()->json(['error' => 'Valid'], 403);
    }
}
