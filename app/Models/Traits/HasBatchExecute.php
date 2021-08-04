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
      $items->each($callback);
      $result += $items->count();
    } while($items->count() > 0);

    return $result;
  }
}