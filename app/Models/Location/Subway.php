<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subway extends Model
{
    use SoftDeletes;

    protected $fillable = [
      'name', 'color', 'line', 'identifier', 'district_id',
    ];

    /**
     * Relation to the city
     *
     * @return BelongsTo
    */
    public function city(): BelongsTo {
      return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * Relation to the district
     *
     * @return BelongsTo
    */
    public function district(): BelongsTo {
      return $this->belongsTo(District::class, 'district_id');
    }
}
