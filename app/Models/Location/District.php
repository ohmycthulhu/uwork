<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class District extends Model
{
    use SoftDeletes, HasTranslations;

    protected $fillable = ['name'];

    protected $visible = ['id', 'name', 'city', 'city_idz'];

    public $translatable = ['name'];

    /**
     * Relations
    */

    /**
     * Relation to city
     *
     * @return BelongsTo
    */
    public function city(): BelongsTo {
      return $this->belongsTo(City::class, 'city_id');
    }
}
