<?php

namespace App\Models\Info;

use App\Models\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use SoftDeletes, HasTranslations;

    public $translatable = ['question', 'answer'];

    protected static function boot()
    {
      parent::boot();
      static::addGlobalScope(new OrderScope('order', 'asc'));
    }
}
