<?php

namespace App\Models\Complaints;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplaintType extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    /**
     * Relation to complaints
     *
     * @return HasMany
    */
    public function complaints():HasMany {
      return $this->hasMany(Complaint::class, 'type_id');
    }
}
