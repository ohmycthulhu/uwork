<?php

namespace App\Models\Info;

use App\Models\Interfaces\Slugable;
use App\Models\Traits\SlugableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpItem extends Model implements Slugable
{
  use SoftDeletes, SlugableTrait;

  protected $fillable = [
    'name', 'text', 'order'
  ];

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
