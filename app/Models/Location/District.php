<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class District extends Model
{
  use SoftDeletes, HasTranslations;

  protected $fillable = ['name'];

  protected $visible = ['id', 'name', 'city', 'city_id'];

  public $translatable = ['name'];

  /**
   * Scopes
   */

  /**
   * Scope to filter by name
   *
   * @param Builder $query
   * @param string $name
   *
   * @return Builder
   */
  public function scopeName(Builder $query, string $name): Builder
  {
    return $query->where('name', "like", "%\"$name\"%");
  }

  /**
   * Relations
   */

  /**
   * Relation to city
   *
   * @return BelongsTo
   */
  public function city(): BelongsTo
  {
    return $this->belongsTo(City::class, 'city_id');
  }
}
