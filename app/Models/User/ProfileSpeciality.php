<?php

namespace App\Models\User;

use App\Models\Categories\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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
   * Relation to users by favourite
   *
   * @return BelongsToMany
  */
  public function favouriteBy(): BelongsToMany {
    return $this->belongsToMany(
      User::class,
      'user_favourite_services',
      'service_id',
      'user_id'
    );
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

  /**
   * Attribute to check if user already selected this speciality as favourite
   *
   * @return bool
  */
  public function getIsFavouriteAttribute(): bool {
    if (!Auth::id()) {
      return false;
    }
    return !!$this->favouriteBy()
      ->find(Auth::id())
      ->first();
  }
}
