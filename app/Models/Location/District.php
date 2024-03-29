<?php

namespace App\Models\Location;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
  use SoftDeletes;

  protected $fillable = ['name'];

  protected $visible = ['id', 'name', 'city', 'city_id'];

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

  /**
   * Relation to subways
   *
   * @return HasMany
   */
  public function subways(): HasMany {
    return $this->hasMany(Subway::class, 'district_id');
  }

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
    return $query->where('name', "like", $name);
  }
}
