<?php

namespace App\Models\Media;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\Models\Media;

class Image extends Media
{
  protected $table = 'media';

  protected $appends = ['url'];

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
   * Sets additional model
   *
   * @param string $modelType
   * @param int $modelId
   *
   * @return $this
  */
  public function setAdditionalModel(string $modelType, int $modelId): Image {
    $this->model_additional_type = $modelType;
    $this->model_additional_id = $modelId;
    $this->save();
    return $this;
  }

  /**
   * Relation to additional model
   *
   * @return MorphTo
   */
  public function modelAdditional(): MorphTo {
    return $this->morphTo();
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

  /**
   * Scope by media information
   *
   * @param Builder $query
   * @param string $type
   * @param int $id
   *
   * @return Builder
  */
  public function scopeMedia(Builder $query, string $type, int $id): Builder {
    return $query->where('model_type', $type)
      ->where('id', $id);
  }

  /**
   * Gets url attribute
   *
   * @return string
  */
  public function getUrlAttribute(): string {
    return config('app.url').$this->getUrl();
  }
}
