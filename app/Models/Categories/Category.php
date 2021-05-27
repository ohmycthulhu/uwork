<?php

namespace App\Models\Categories;

use App\Models\Interfaces\Slugable;
use App\Models\Model;
use App\Models\Traits\SlugableTrait;
use App\Models\User\Profile;
use ElasticScoutDriverPlus\CustomSearch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Category extends Model implements Slugable
{
  use SoftDeletes, SlugableTrait, Searchable, CustomSearch;

  // Route to specific category
  public static $slugRoute = 'api.categories.slug';

  protected $fillable = ['name', 'icon_default', 'icon_selected'];

  protected $visible = ['id', 'name', 'slug', 'parent_id', 'children', 'parent', 'is_hidden', 'is_shown', 'category_path'];

  protected $appends = ['is_shown'];

  /**
   * Method to search similar categories
   *
   * @param string $name
   * @param ?int $parentId
   * @param int $size
   *
   * @return Collection
   */
  public static function searchByName(string $name, ?int $parentId = null, int $size = 10): Collection
  {
    $nameToSearch = Str::lower(trim(($name)));
    $keyword = str_replace(" ", "*", $nameToSearch);
    $query = static::boolSearch()
      ->size($size);

    if ($keyword) {
      $query->should(['wildcard' => ['name' => "*$keyword*"]])
        ->minimumShouldMatch(1);
    }

    if ($parentId) {
      $query->must(['wildcard' => ['category_path' => "*$parentId*"]]);
    }

    return $query->execute()
      ->models();
  }

  /**
   * Method to append total and count fields for services
   *
   * @param Collection $categories
   * @param ?Profile $profile
   * @param ?int $parentId
   * @param bool $addTotal
   * @param bool $addServicesList
   *
   * @return Collection
   */
  public static function addServicesFields(
    Collection $categories,
    ?Profile $profile = null,
    ?int $parentId = null,
    bool $addTotal = false,
    bool $addServicesList = false
  ): Collection
  {
    $countCallback = function () {
      return 0;
    };
    $isSelectedCallback = function () {
      return false;
    };
    if ($profile) {
      /* @var Collection $specialities */
      $specialities = $profile->specialities()
        ->category($parentId)
        ->pluck('category_path', 'category_id');
      if (!empty($specialities)) {
        $countCallback = function (Category $category) use ($specialities) {
          return $specialities->filter(function (string $path) use ($category) {
            return str_contains($path, " {$category->id} ");
          })->count();
        };
        $isSelectedCallback = function (Category $category, int $count, ?int $total) use ($specialities) {
          return !$total ? $specialities->has($category->id) : ($total <= $count);
        };
      }
    }

    return $categories->map(function (Category $category) use ($addTotal, $addServicesList, $countCallback, $isSelectedCallback) {
      $total = $addTotal ? $category->getServicesCountAttribute() : null;
      $services = $addServicesList ? $category->getServicesAttribute() : null;
      $count = $countCallback($category);
      return array_merge(
        ['category' => $category],
        $total !== null ? compact('total') : [],
        $services !== null ? compact('services') : [],
        [
          'count' => $count,
          'selected' => $isSelectedCallback($category, $count, $total),
        ]
      );
    });
  }

  /**
   * Relation to parent category
   *
   * @return BelongsTo
   */
  public function parent(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'parent_id');
  }

  /**
   * Relation to children
   *
   * @return HasMany
   */
  public function children(): HasMany
  {
    return $this->hasMany(Category::class, 'parent_id')->visible();
  }

  /*
   * Scopes section
   */
  /**
   * Scope by parent
   *
   * @param Builder $query
   * @param int $categoryId
   *
   * @return Builder
   */
  public function scopeParent(Builder $query, int $categoryId): Builder
  {
    return $query->where('parent_id', $categoryId);
  }

  /**
   * Search by name
   *
   * @param Builder $query
   * @param string $keyword
   *
   * @return Builder
   */
  public function scopeKeyword(Builder $query, string $keyword): Builder
  {
    return $query->where('name', 'LIKE', "%$keyword%");
  }

  /**
   * Scope by full name
   *
   * @param Builder $query
   * @param string $name
   *
   * @return Builder
   */
  public function scopeName(Builder $query, string $name): Builder
  {
    return $query->where('name', 'like', "%\"$name\"%");
  }

  /**
   * Scope only top level categories
   *
   * @param Builder $query
   *
   * @return Builder
   */
  public function scopeTop(Builder $query): Builder
  {
    return $query->whereNull('parent_id');
  }

  /**
   * Scope only non-top level categories
   *
   * @param Builder $query
   *
   * @return Builder
   */
  public function scopeChild(Builder $query): Builder
  {
    return $query->whereNotNull('parent_id');
  }

  /**
   * Scope to get only visible
   *
   * @param Builder $query
   *
   * @return Builder
   */
  public function scopeVisible(Builder $query): Builder
  {
    return $query->where('is_hidden', false);
  }

  /**
   * Scope to get certain id
   *
   * @param Builder $query
   * @param int $id
   *
   * @return Builder
   */
  public function scopeId(Builder $query, int $id): Builder
  {
    return $query->where('id', $id);
  }

  /**
   * Get array of searchable columns
   *
   * @return array
   */
  public function toSearchableArray(): array
  {
    return [
      'id' => $this->id,
      'parent_id' => $this->parent_id,
      'name' => Str::lower($this->name),
      'category_path' => $this->category_path,
    ];
  }

  /**
   * Attribute to show if category should be shown
   * It is calculated depending on is_hidden field
   *
   * @return boolean
   */
  public function getIsShownAttribute(): bool
  {
    return !$this->is_hidden;
  }

  /**
   * Attribute to load list of services
   *
   * @return Collection
   */
  public function getServicesAttribute(): Collection
  {
    return $this->getServicesQuery()
      ->get();
  }

  /**
   * Attribute to get number of services
   *
   * @return int
   */
  public function getServicesCountAttribute(): int
  {
    return $this->getServicesQuery()
      ->count();
  }

  /**
   * Method to create query for services
   *
   * @return Builder
   */
  public function getServicesQuery(): Builder
  {
    return CategoryService::query()
      ->category($this->id);
  }
}
