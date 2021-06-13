<?php

namespace App\Models\Location;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
  use SoftDeletes;

  // Fillable
  protected $fillable = ['name', 'google_id'];

  protected $visible = ['id', 'name', 'cities'];

  /*
   * Relations
   * */

  /**
   * Relation to cities
   *
   * @return HasMany
   */
  public function cities(): HasMany
  {
    return $this->hasMany(City::class, 'region_id');
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
   */

  /**
   * Link to regions
   *
   * @return string
   */
  public function getLinkAttribute(): string
  {
    return route('api.regions.id', ['id' => $this->id]);
  }

  /**
   * Link to region's cities
   *
   * @return string
   */
  public function getLinkCitiesAttribute(): string
  {
    return route('api.regions.id.cities', ['id' => $this->id]);
  }
}
