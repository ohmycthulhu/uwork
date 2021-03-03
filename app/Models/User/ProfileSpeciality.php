<?php

namespace App\Models\User;

use App\Models\Categories\Category;
use App\Models\Media\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ProfileSpeciality extends Model
{

  // Fillable
  protected $fillable = [
    'category_id', 'price', 'name',
  ];

//  protected $hidden = [
//    'category_path'
//  ];

  protected $with = [
    'category'
  ];

  /**
   * Method to update speciality
   *
   * @param ?float $price
   * @param ?string $name
   *
   * @return $this
  */
  public function updateInfo(?float $price, ?string $name): ProfileSpeciality {
    if ($price) {
      $this->price = $price;
    }
    if ($name) {
      $this->name = $name;
    }
    if ($this->isDirty()) {
      $this->save();
    }
    return $this;
  }

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
   * Relation to media images
   *
   * @return MorphMany
  */
  public function media(): MorphMany {
    return $this->morphMany(Image::class, 'modelAdditional');
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
   * Scope by exact speciality
   *
   * @param Builder $query
   * @param string $name
   * @param int $categoryId
   *
   * @return Builder
  */
  public function scopeExact(Builder $query, string $name, int $categoryId): Builder {
    return $this->where('name', 'like', $name)
      ->where('category_id', $categoryId);
  }

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
