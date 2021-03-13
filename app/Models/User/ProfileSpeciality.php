<?php

namespace App\Models\User;

use App\Models\Categories\Category;
use App\Models\Media\Image;
use App\Models\Model;
use App\Models\User;
use ElasticScoutDriverPlus\CustomSearch;
use ElasticScoutDriverPlus\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

class ProfileSpeciality extends Model
{
  use Searchable, CustomSearch;

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
  public function updateInfo(?float $price, ?string $name): ProfileSpeciality
  {
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
   * Relation to media images
   *
   * @return MorphMany
   */
  public function media(): MorphMany
  {
    return $this->morphMany(Image::class, 'modelAdditional');
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
   * @param int $categoryId
   *
   * @return Builder
   */
  public function scopeCategory(Builder $query, int $categoryId): Builder
  {
    return $query->where('category_path', 'LIKE', "%|$categoryId|%");
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

  /*
   * Search section
   */

  /**
   * Returns values for indexing
   *
   * @return array
   */
  public function toSearchableArray(): array
  {
    if (!$this->profile && $this->profile_id) {
      $this->load('profile');
    }
    $profile = $this->profile;
    $locationInfo = $profile ? [
      'region_id' => $profile->region_id,
      'city_id' => $profile->region_id,
      'district_id' => $profile->region_id,
      'user_id' => $profile->user_id,
    ] : [];

    return array_merge([
      'id' => $this->id,
      'parent_id' => $this->profile_id,
      'cat_path' => $this->category_path,
      'price' => $this->price,
    ], $locationInfo);
  }

  /**
   * Performs advanced search
   *
   * @param ?int $categoryId
   * @param ?int[] $categories
   * @param ?int $regionId ,
   * @param ?int $cityId ,
   * @param ?int $districtId ,
   * @param ?int $userId ,
   * @param ?int $page
   * @param int $amount
   *
   * @return Paginator
   */
  public static function completeSearch(
    ?int $categoryId,
    ?array $categories,
    ?int $regionId,
    ?int $cityId,
    ?int $districtId,
    ?int $userId,
    ?int $page = 1,
    int $amount = 5
  ): Paginator
  {
    $query = static::boolSearch();

    $locations = [];
    /* Prepare location constraints */
    if ($regionId) {
      $locations["region_id"] = $regionId;
    }
    if ($cityId) {
      $locations["city_id"] = $cityId;
    }
    if ($districtId) {
      $locations["district_id"] = $districtId;
    }
    if ($locations) {
      $query->must(["match" => $locations]);
    }

    /* Prepare category filter */
    if ($categoryId) {
      $strictCategory = ["wildcard" => ["cat_path" => "*|$categoryId|*"]];
      $query->must($strictCategory);
    }

    foreach (($categories ?? []) as $cat) {
      $query->should(["wildcard" => ["cat_path" => "*|$cat|*"]]);
    }

    if ($userId) {
      $userConstraints = ["user_id" => $userId];
      $query->mustNot($userConstraints);
    }

    if (!$locations && !$categories && !$categoryId) {
      $query->mustNot(['match' => ['id' => ""]]);
    }

    /* Perform search */
    return $query
      ->aggregate("parent", ["terms" => ["field" => "parent_id"]])
      ->paginate($amount, 'page', $page);
  }

  /*
   * End of section
   */
}
