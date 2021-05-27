<?php

namespace App\Models\Info;

use App\Models\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
      parent::boot();
      static::addGlobalScope(new OrderScope('order', 'asc'));
    }
}
