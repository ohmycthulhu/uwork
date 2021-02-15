<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Region extends Model
{
  use SoftDeletes, HasTranslations;
  // Fillable
  protected $fillable = ['name'];

  // Translatable
  public $translatable = ['name'];

  protected $visible = ['id', 'name', 'cities'];

  /*
   * Relations
   * */

  /**
   * Relation to cities
   *
   * @return HasMany
  */
  public function cities(): HasMany {
    return $this->hasMany(City::class, 'region_id');
  }

  /**
   * Scopes
  */

  /**
   * Attributes
  */

  /**
   * Link to regions
   *
   * @return string
  */
  public function getLinkAttribute(): string {
    return route('api.regions.id', ['id' => $this->id]);
  }

  /**
   * Link to region's cities
   *
   * @return string
  */
  public function getLinkCitiesAttribute(): string {
    return route('api.regions.id.cities', ['id' => $this->id]);
  }
}
