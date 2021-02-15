<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
  use SoftDeletes, HasTranslations;

  // Fillable
  protected $fillable = ['name'];

  // Translatable
  public $translatable = ['name'];

  protected $visible = ['id', 'name', 'region', 'region_id', 'districts'];

  /**
   * Relations
  */

  /**
   * Relation to region
   *
   * @return BelongsTo
  */
  public function region(): BelongsTo {
    return $this->belongsTo(Region::class, 'region_id');
  }

  /**
   * Relation to districts
   *
   * @return HasMany
  */
  public function districts(): HasMany {
    return $this->hasMany(District::class, 'city_id');
  }

  /**
   * Attributes
   * */

  /**
   * Link to regions
   *
   * @return string
   */
  public function getLinkAttribute(): string {
    return route('api.cities.id', ['id' => $this->id]);
  }

  /**
   * Link to region's cities
   *
   * @return string
   */
  public function getLinkDistrictsAttribute(): string {
    return route('api.cities.id.districts', ['id' => $this->id]);
  }
}
