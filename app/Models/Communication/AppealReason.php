<?php

namespace App\Models\Communication;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class AppealReason extends Model
{
    use SoftDeletes, HasTranslations;

    protected $fillable = [
      'name'
    ];

    public $translatable = [
      'name'
    ];

    /**
     * Relations
    */

    /**
     * Relation to appeals
     *
     * @return HasMany
    */
    public function appeals(): HasMany {
      return $this->hasMany(Appeal::class, 'appeal_reason_id');
    }
}
