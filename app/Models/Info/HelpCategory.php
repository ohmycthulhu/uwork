<?php

namespace App\Models\Info;

use App\Models\Interfaces\Slugable;
use App\Models\Scopes\OrderScope;
use App\Models\Traits\SlugableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpCategory extends Model implements Slugable
{
    use SoftDeletes, SlugableTrait;

    protected $fillable = ['name', 'order'];

    public static $slugRoute = 'api.helpCategories.slug';

    protected static function boot()
    {
      parent::boot();
      self::addGlobalScope(new OrderScope('order', 'asc'));
    }

    /**
     * Relation to items
     *
     * @return HasMany
    */
    public function items(): HasMany {
      return $this->hasMany(HelpItem::class, 'help_category_id');
    }

    /**
     * Relation to top items
     *
     * @return HasMany
    */
    public function topItems(): HasMany {
      return $this->items()->take(3);
    }
}
