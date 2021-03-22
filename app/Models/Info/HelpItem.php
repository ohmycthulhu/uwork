<?php

namespace App\Models\Info;

use App\Models\Interfaces\Slugable;
use App\Models\Traits\SlugableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class HelpItem extends Model implements Slugable
{
  use SoftDeletes, HasTranslations, SlugableTrait;

  protected $fillable = [
    'name', 'text', 'order'
  ];

  public $translatable = ['name', 'text', 'slug'];

  public static $slugRoute = 'api.helpItems.slug';

  /**
   * Relation to category
   *
   * @return BelongsTo
   */
  public function category(): BelongsTo
  {
    return $this->belongsTo(HelpCategory::class, 'help_category_id');
  }
}
