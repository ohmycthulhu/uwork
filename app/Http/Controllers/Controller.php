<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Method to return error
     *
     * @param string $error
     * @param int $status
     * @param array $data
     *
     * @return JsonResponse
    */
    protected function returnError(string $error, int $status, array $data = []): JsonResponse {
      return response()->json(array_merge([
        'status' => 'error',
        'error' => $error,
      ], $data), $status);
    }

    /**
     * Method to return successful response
     *
     * @param ?array $result
     * @param int $status
     *
     * @return JsonResponse
    */
    protected function returnSuccess(?array $result = null, int $status = 200): JsonResponse {
      return response()->json(
        array_merge(
          ['status' => 'success'],
          $result ?? []
        ), $status
      );
    }
}
