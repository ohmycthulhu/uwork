<?php

namespace App\Models\Media;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\Models\Media;

class Image extends Media
{
  protected $table = 'media';
  /**
   * Method to attach empty elements to model
   *
   * @param string $modelType
   * @param int $modelId
   * @param array $mediaIds
   *
   * @return Collection
   */
  public static function attachMedia(string $modelType, int $modelId, array $mediaIds): Collection
  {
    $query = self::query();
    $query->empty()
      ->ids($mediaIds);

    $query->update(['model_type' => $modelType, 'model_id' => $modelId]);

    return $query->get();
  }

  /**
   * Scope by ids
   *
   * @param Builder $query
   * @param array $ids
   *
   * @return Builder
   */
  public function scopeIds(Builder $query, array $ids): Builder
  {
    return $query->whereIn('id', $ids);
  }

  /**
   * Scope by empty
   *
   * @param Builder $query
   *
   * @return Builder
   */
  public function scopeEmpty(Builder $query): Builder
  {
    return $query->where('model_type', '')
      ->where('model_id', 0);
  }
}
