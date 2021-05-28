<?php

namespace App\Models\User;

use App\Facades\NotificationFacade;
use App\Facades\SearchFacade;
use App\Models\Categories\Category;
use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\Region;
use App\Models\Model;
use App\Models\Profile\ProfileView;
use App\Models\Profile\Review;
use App\Models\Traits\HasComplaints;
use App\Models\User;
use ElasticScoutDriverPlus\CustomSearch;
use ElasticScoutDriverPlus\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Profile extends Model
{
  const SORT_PRICE = 'price_avg';

  use SoftDeletes, Searchable, CustomSearch, HasComplaints;

  // Fillable fields
  protected $fillable = [
    'about', 'phone', 'picture', 'reviews_count', 'rating',
    'views_count', 'open_count',
  ];

  // Hidden fields
  protected $hidden = [
    'reviewed_at',
  ];

  protected $appends = [
    'is_approved',
  ];

  protected $reviewRatingTypes = [
    'rating_time',
    'rating_quality',
    'rating_price',
  ];

  /**
   * Methods
   */

  /**
   * Method to synchronize rating and reviews count
   *
   * @return $this
   */
  public function synchronizeReviews(): Profile
  {
    $reviewsCount = $this->reviews()->count();
    if ($reviewsCount != $this->reviews_count) {
      $this->reviews_count = $reviewsCount;
      // Calculate each rating
      $ratingTypes = $this->reviewRatingTypes;
      foreach ($ratingTypes as $index => $type) {
        $this->{$type} = $reviewsCount > 0 ? ($this->reviews()->sum($type) / $reviewsCount) : 0;
      }

      // Update overall rating
      if ($reviewsCount > 0) {
        $ratProd = array_reduce($ratingTypes, function ($acc, $type) {
          return $acc * $this->{$type} / 5;
        }, 1);
        $ratSum = array_reduce($ratingTypes, function ($acc, $type) {
          return $acc + $this->{$type} / 5;
        }, 0);
        $this->rating = sizeof($ratingTypes) * $ratProd / $ratSum;
        $this->positive_rating_ratio = $this->reviews()->averageRating(3)->count() / $reviewsCount;
      } else {
        $this->rating = 0;
        $this->positive_rating_ratio = 0;
      }

      $this->save();
    }

    return $this;
  }

  /**
   * Method to synchronize views
   *
   * @return $this
   */
  public function synchronizeViews(): Profile
  {
    $viewsCount = $this->views()->count();
    $openCount = $this->views()->open()->count();
    if ($viewsCount != $this->views_count || $openCount != $this->open_count) {
      $this->views_count = $viewsCount;
      $this->open_count = $openCount;
      $this->phone_display_count = $openCount;
      $this->save();
    }
    return $this;
  }

  /**
   * Method to set the phone
   *
   * @param string $phone
   * @param bool $verified
   *
   * @return $this
   */
  public function setPhone(string $phone, bool $verified): Profile
  {
    $this->phone = $phone;
    $this->phone_verified = $verified;
    $this->save();
    return $this;
  }

  /**
   * Returns current phone
   *
   * @return string
   */
  public function getPhone(): string
  {
    return $this->phone;
  }

  /**
   * Method to update profile information
   *
   * @param ?string $about
   *
   * @return $this
   */
  public function setInfo(?string $about): Profile
  {
    if ($about) {
      $this->about = $about;
    }

    if ($this->isDirty()) {
      $this->save();
    }
    return $this;
  }

  /**
   * Method to add specialities
   *
   * @param int $categoryId
   * @param float $price
   * @param ?string $name
   * @param ?string $description
   *
   * @return Model|\Illuminate\Database\Eloquent\Model
   */
  public function addSpeciality(int $categoryId, float $price, ?string $name = null, ?string $description = null): Model
  {
    return $this->specialities()
      ->create(['category_id' => $categoryId, 'price' => $price, 'name' => $name, 'description' => $description]);
  }

  /**
   * Method to remove speciality
   *
   * @param int $categoryId
   *
   * @return int
   */
  public function removeSpeciality(int $categoryId): int
  {
    return $this->specialities()
      ->categoryId($categoryId)
      ->delete();
  }

  /**
   * Relations
   */
  /**
   * Relation to user
   *
   * @return BelongsTo
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  /**
   * Relation to specialities
   *
   * @return HasMany
   */
  public function specialities(): HasMany
  {
    return $this->hasMany(ProfileSpeciality::class, 'profile_id');
  }

  /**
   * Relation to reviews
   *
   * @return HasMany
   */
  public function reviews(): HasMany
  {
    return $this->hasMany(Review::class, 'profile_id')->top();
  }

  /**
   * Relation to views
   *
   * @return HasMany
   */
  public function views(): HasMany
  {
    return $this->hasMany(ProfileView::class, 'profile_id');
  }

  /**
   * Location Region
   *
   * @return BelongsTo
  */
  public function region(): BelongsTo {
    return $this->belongsTo(Region::class, 'region_id');
  }

  /**
   * Location City
   *
   * @return BelongsTo
  */
  public function city(): BelongsTo {
    return $this->belongsTo(City::class, 'city_id');
  }

  /**
   * Location District
   *
   * @return BelongsTo
  */
  public function district(): BelongsTo {
    return $this->belongsTo(District::class, 'district_id');
  }

  /**
   * Confirm the profile
   *
   * @return $this
  */
  public function confirm(): self {
    $this->confirmed_at = date('Y-m-d H:i:s');
    $this->failed_audition = false;
    $this->save();
    NotificationFacade::create(
      $this->user()->first(),
      static::class,
      $this->id,
      'Профиль подтверждён',
      null
    );
    return $this;
  }

  /**
   * Reject the profile
   *
   * @return $this
  */
  public function reject(): self {
    $this->confirmed_at = null;
    $this->failed_audition = true;
    $this->save();
    NotificationFacade::create(
      $this->user()->first(),
      static::class,
      $this->id,
      'Профиль отклонён',
      null
    );
    return $this;
  }

  /**
   * Scopes
   */

  /**
   * Scope by visibility
   *
   * @param Builder $query
   * @param bool $visible
   *
   * @return Builder
   */
  public function scopeVisible(Builder $query, bool $visible): Builder
  {
    return $query->where('is_hidden', !$visible);
  }

  /**
   * Scope publicity
   *
   * @param Builder $query
   *
   * @return Builder
   */
  public function scopePublic(Builder $query): Builder
  {
    return $query->whereNotNull('confirmed_at')
      ->where('failed_audition', false)
      ->visible(true);
  }

  /**
   * Scope region
   *
   * @param Builder $query
   * @param int $regionId
   *
   * @return Builder
   */
  public function scopeRegion(Builder $query, int $regionId): Builder
  {
    return $query->where('region_id', $regionId);
  }

  /**
   * Scope city
   *
   * @param Builder $query
   * @param int $cityId
   *
   * @return Builder
   */
  public function scopeCity(Builder $query, int $cityId): Builder
  {
    return $query->where('city_id', $cityId);
  }

  /**
   * Scope district
   *
   * @param Builder $query
   * @param int $districtId
   *
   * @return Builder
   */
  public function scopeDistrict(Builder $query, int $districtId): Builder
  {
    return $query->where('district_id', $districtId);
  }

  /**
   * Scope by exact category
   *
   * @param Builder $query
   * @param Category $category
   *
   * @return Builder
   */
  public function scopeExactCategory(Builder $query, Category $category): Builder
  {
    return $query->whereHas('specialities', function ($q) use ($category) {
      return $q->categoryId($category->id);
    });
  }

  /**
   * Scope flexibly by category
   *
   * @param Builder $query
   * @param int $categoryId
   *
   * @return Builder
   */
  public function scopeCategory(Builder $query, int $categoryId): Builder
  {
    return $query->wherehas('specialities', function ($q) use ($categoryId) {
      return $q->category($categoryId);
    });
  }

  /**
   * Scope profiles not belonging to the user
   *
   * @param Builder $query
   * @param User $user
   *
   * @return Builder
   */
  public function scopeNotUser(Builder $query, User $user): Builder
  {
    return $query->where('user_id', '<>', $user->id);
  }

  /**
   * Attributes
   */

  /**
   * Attribute to check the status
   *
   * @return bool
   */
  public function getIsApprovedAttribute(): bool
  {
    return !!$this->confirmed_at && !$this->failed_audition;
  }

  /**
   * Search section
   *
   */

  /**
   * Converts model to indices
   *
   * @return array
   */
  public function toSearchableArray(): array
  {
    $specialities = $this->specialities()
      ->get();
    $specialitiesInfo = $specialities->map(function (ProfileSpeciality $speciality) {
        return [
          'price' => $speciality->price,
          'categoryId' => $speciality->category_id,
          'catPath' => SearchFacade::calculateCategoryPath($speciality->category_id),
        ];
      })->toArray();
    $prices = $specialities->pluck('price')->filter(function ($p) { return $p; });

    return [
      'id' => $this->id,
      'regionId' => $this->region_id,
      'cityId' => $this->city_id,
      'districtId' => $this->district_id,
      'userId' => $this->user_id,
      'specialities' => $specialitiesInfo,
      'isConfirmed' => $this->confirmed_at ? 1 : 0,
      'price_avg' => $prices->average(),
      'price_min' => $prices->min(),
      'price_max' => $prices->max()
    ];
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
    ?float $priceMin,
    ?float $priceMax,
    $searchColumn = null,
    $searchDir = 'asc',
    ?int $page = 1,
    int $amount = 5
  ): Paginator
  {
    $query = static::boolSearch();

    $locations = [];
    /* Prepare location constraints */
    if ($districtId) {
      $locations["districtId"] = $districtId;
    } elseif ($cityId) {
      $locations["cityId"] = $cityId;
    } elseif ($regionId) {
      $locations["regionId"] = $regionId;
    }
    if ($locations) {
      $query->must(["match" => $locations]);
    }

    /* Prepare category filter */
    if ($categoryId) {
      $strictCategory = ["match" => ["specialities.catPath" => $categoryId]];
      $query->must($strictCategory);
    }

    if ($priceMax != null || $priceMin != null) {
      $range = [];
      if ($priceMin != null) $range['gte'] = $priceMin;
      if ($priceMax != null) $range['lte'] = $priceMax;
      $query->must(['range' => ['specialities.price' => $range]]);
    }

    foreach (($categories ?? []) as $cat) {
      $query->should(["match" => ["specialities.categoryId" => $cat]]);
    }

    if ($userId) {
      $userConstraints = ['match' => ["user_id" => $userId]];
      $query->mustNot($userConstraints);
    }

    // Filter only by confirmed
    $query->must(['match' => ['isConfirmed' => "1"]]);
    $query->minimumShouldMatch(empty($categories) ? 0 : 1);

    if ($searchColumn) {
      $query->sort($searchColumn, $searchDir);
    }

    /* Perform search */
    return $query
      ->paginate($amount, 'page', $page);
  }
}
