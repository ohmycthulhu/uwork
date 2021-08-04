<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasBatchExecute
{
  /**
   * Scope for applying batch executing to the query
   *
   * @param Builder $query
   * @param callback $callback
   * @param int $batchSize
   *
   * @return int
   */
  protected function scopeBatchExecute(Builder $query, callable $callback, int $batchSize): int {
    $result = 0;

    do {
      $items = (clone $query)->limit($batchSize)->skip($result)->get();
      foreach ($items as $model) {
        $callback($model, $result++);
      }
    } while($items->count() > 0);

    return $result;
  }
}