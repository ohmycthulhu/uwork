<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Info\TextStatistic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TextStatisticsController extends Controller
{
    protected $textStatistics;

    /**
     * Creates new instance
     *
     * @param TextStatistic $statistic
    */
    public function __construct(TextStatistic $statistic)
    {
      $this->textStatistics = $statistic;
    }

    /**
     * Method to get text
     *
     * @param string $type
     *
     * @return JsonResponse
    */
    public function getText(string $type): JsonResponse {
      $text = $this->textStatistics::query()
        ->type($type)
        ->first();

      return $this->returnSuccess(compact('text'));
    }

    /**
     * Method to add upvotes
     *
     * @param string $type
     *
     * @return JsonResponse
    */
    public function addUpvote(string $type): JsonResponse {
      $amount = $this->textStatistics::incrementByType($type);
      return $this->returnSuccess(compact('amount'));
    }

    /**
     * Method to add downvotes
     *
     * @param string $type
     *
     * @return JsonResponse
    */
    public function addDownvote(string $type): JsonResponse {
      $amount = $this->textStatistics::decrementByType($type);
      return $this->returnSuccess(compact('amount'));
    }
}
