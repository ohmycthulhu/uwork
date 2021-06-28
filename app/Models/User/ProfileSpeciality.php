<?php

namespace App\Models\User;

use App\Models\Categories\Category;
use App\Models\Media\Image;
use App\Models\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class ProfileSpeciality extends Model implements HasMedia
{
  use HasMediaTrait;

  // Fillable
  protected $fillable = [
    'category_id', 'price', 'name', 'description',
  ];

//  protected $hidden = [
//    'category_path'
//  ];

  protected $with = [
    'category', 'media',
  ];

  /**
   * Method to map specialities to include full category path
   *
   * @param Collection $specialities
   *
   * @return Collection
  */
  public static function includeCategoriesPath(Collection $specialities): Collection {
    // Get unique ids for categories
    $categoriesIds = $specialities->reduce(function (array $acc, ProfileSpeciality $speciality) {
      return array_unique(array_merge($acc, $speciality->getCategoryPathIdsAttribute()));
    }, []);

    // Load categories into memory
    $categories = Category::query()
      ->whereIn('id', $categoriesIds)
      ->get()
      ->reduce(function (array $acc, Category $category) { $acc[$category->id] = $category; return $acc; }, []);

    // Map categories' ids list into categories list
    return $specialities->map(function (ProfileSpeciality $speciality) use ($categories) {
      $categories = array_map(
        function ($id) use ($categories) { return $categories[$id] ?? null; },
        $speciality->getCategoryPathIdsAttribute()
      );
      return array_merge($speciality->toArray(), ['categories' => $categories]);
    });
  }

  /**
   * Method for including is favourite field
   *
   * @param Collection $specialities
   * @param ?User      $user
   *
   * @return Collection
  */
  public static function includeIsFavouriteField(Collection $specialities, ?User $user): Collection {
    if ($user) {
      $specIds = $user->favouriteServices()
        ->whereIn('profile_specialities.id', $specialities->pluck('id'))
        ->pluck('profile_specialities.id');
      $isFavouriteCallback = function ($id) use ($specIds) {
        return $specIds->contains($id);
      };
    } else {
      $isFavouriteCallback = function () { return false; };
    }

    return $specialities->map(function ($speciality) use ($isFavouriteCallback) {
      return array_merge(
        is_array($speciality) ? $speciality : $speciality->toArray(),
        ['is_favourite' => $isFavouriteCallback($speciality['id'])]
      );
    });
  }

  /**
   * Method to update speciality
   *
   * @param ?float $price
   * @param ?string $name
   * @param ?string $description
   *
   * @return $this
   */
  public function updateInfo(?float $price, ?string $name, ?string $description): ProfileSpeciality
  {
    if ($price) {
      $this->price = $price;
    }
    if ($name) {
      $this->name = $name;
    }
    if ($description) {
      $this->description = $description;
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
  public function profile(): BelongsTo
  {
    return $this->belongsTo(Profile::class, 'profile_id');
  }

  /**
   * Relation to categories
   *
   * @return BelongsTo
   */
  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'category_id');
  }

  /**
   * Override media relation
   *
   * @return MorphMany
   */
  public function media(): MorphMany
  {
    return $this->morphMany(Image::class, 'model')
      ->orderBy('order_column');
  }

  /**
   * Relation to users by favourite
   *
   * @return BelongsToMany
   */
  public function favouriteBy(): BelongsToMany
  {
    return $this->belongsToMany(
      User::class,
      'user_favourite_services',
      'service_id',
      'user_id'
    );
  }

  /**
   * Checks if speciality belongs to category
   *
   * @param ?int $categoryId
   *
   * @return bool
   */
  public function belongsToCategory(?int $categoryId) {
    return Str::contains($this->category_path, " {$categoryId} ");
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
  public function scopeExact(Builder $query, string $name, int $categoryId): Builder
  {
    return $query->where('name', 'like', $name)
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
  public function scopeCategoryId(Builder $query, int $categoryId): Builder
  {
    return $query->where('category_id', $categoryId);
  }

  /**
   * Scope for searching flexible by category
   *
   * @param Builder $query
   * @param ?int $categoryId
   *
   * @return Builder
   */
  public function scopeCategory(Builder $query, ?int $categoryId): Builder
  {
    if ($categoryId) {
      $query->where('category_path', 'LIKE', "% $categoryId %");
    }
    return $query;
  }

  /**
   * Attribute to check if user already selected this speciality as favourite
   *
   * @return bool
   */
  public function getIsFavouriteAttribute(): bool
  {
    if (!Auth::id()) {
      return false;
    }
    return !!$this->favouriteBy()
      ->find(Auth::id())
      ->first();
  }

  /**
   * Attribute to get category path as an array
   *
   * @return array
  */
  public function getCategoryPathIdsAttribute(): array {
    if (!$this->category_path) {
      return [];
    }
    return explode('  ', trim($this->category_path));
  }
}
