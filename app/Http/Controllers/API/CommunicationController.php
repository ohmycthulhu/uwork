<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Communication\AppealFormRequest;
use App\Models\Communication\Appeal;
use App\Models\Communication\AppealReason;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunicationController extends Controller
{
  /* @var Appeal $appeal */
    protected $appeal;
    protected $appealReason;

    /**
     * Creates new instance
     *
     * @param Appeal $appeal
     * @param AppealReason $appealReason
    */
    public function __construct(Appeal $appeal, AppealReason $appealReason)
    {
      $this->appeal = $appeal;
      $this->appealReason = $appealReason;
    }

    /**
     * Method creates new appeal
     *
     * @param AppealFormRequest $request
     *
     * @return JsonResponse
    */
    public function create(AppealFormRequest $request): JsonResponse {
      $user = Auth::user();

      try {
        $appeal = $this->appeal::instantiate(
          $request->input('text'),
          $request->input('appeal_reason_id'),
          $request->input('appeal_reason_other'),

          $user,
          $request->input('name'),
          $request->ip(),
          $request->input('phone'),
          $request->input('email')
        );

        return response()->json([
          'status' => 'success',
          'appeal' => $appeal,
        ]);
      } catch (\Exception $exception) {
        return response()->json([
          'status' => 'error',
          'error' => $exception->getMessage()
        ], 405);
      }
    }

    /**
     * Method to get available reasons
     *
     * @return JsonResponse
    */
    public function appealReasons(): JsonResponse {
      return response()->json([
        'reasons' => $this->appealReason::all(),
      ]);
    }
}
