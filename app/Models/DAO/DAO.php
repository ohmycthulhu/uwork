<?php

namespace App\Models\DAO;

use App\Models\User\ProfileSpeciality;
use ElasticScoutDriverPlus\CustomSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;


abstract class DAO extends Model
{
    protected $fillable = [];
}
