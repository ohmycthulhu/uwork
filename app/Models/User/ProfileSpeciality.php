<?php

namespace App\Models\User;

use App\Models\Categories\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileSpeciality extends Model
{

  // Fillable
  protected $fillable = [
    'category_id', 'price',
  ];

//  protected $hidden = [
//    'category_path'
//  ];

  /**
   * Relations
  */

  /**
   * Relation to profile
   *
   * @return BelongsTo
  */
  public function profile(): BelongsTo {
    return $this->belongsTo(Profile::class, 'profile_id');
  }

  /**
   * Relation to categories
   *
   * @return BelongsTo
  */
  public function category(): BelongsTo {
    return $this->belongsTo(Category::class, 'category_id');
  }

  /**
   * Scopes
  */

  /**
   * Scope by category id
   *
   * @param Builder $query
   * @param int $categoryId
   *
   * @return Builder
  */
  public function scopeCategoryId(Builder $query, int $categoryId): Builder {
    return $query->where('category_id', $categoryId);
  }

  /**
   * Scope for searching flexible by category
   *
   * @param Builder $query
   * @param int $categoryId
   *
   * @return Builder
  */
  public function scopeCategory(Builder $query, int $categoryId): Builder {
    return $query->where('category_path', 'LIKE', "%|$categoryId|%");
  }
}
