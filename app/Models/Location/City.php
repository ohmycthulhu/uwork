<?php

namespace App\Models\Location;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
  use SoftDeletes;

  // Fillable
  protected $fillable = ['name', 'google_id'];

  protected $visible = ['id', 'name', 'region', 'region_id', 'districts'];

  /**
   * Relations
   */

  /**
   * Relation to region
   *
   * @return BelongsTo
   */
  public function region(): BelongsTo
  {
    return $this->belongsTo(Region::class, 'region_id');
  }

  /**
   * Relation to districts
   *
   * @return HasMany
   */
  public function districts(): HasMany
  {
    return $this->hasMany(District::class, 'city_id');
  }

  /**
   * Relation to subways
   *
   * @return HasMany
  */
  public function subways(): HasMany {
    return $this->hasMany(Subway::class, 'city_id');
  }

  /**
   * Scopes
   */

  /**
   * Scope to filter by google id
   *
   * @param Builder $query
   * @param string $id
   *
   * @return Builder
   */
  public function scopeGoogleId(Builder $query, string $id): Builder
  {
    return $query->where('google_id', $id);
  }

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

  /**
   * Attributes
   * */

  /**
   * Link to regions
   *
   * @return string
   */
  public function getLinkAttribute(): string
  {
    return route('api.cities.id', ['id' => $this->id]);
  }

  /**
   * Link to region's cities
   *
   * @return string
   */
  public function getLinkDistrictsAttribute(): string
  {
    return route('api.cities.id.districts', ['id' => $this->id]);
  }
}
