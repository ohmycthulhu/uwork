<?php

namespace App\Models\Communication;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppealReason extends Model
{
    protected $fillable = [
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
