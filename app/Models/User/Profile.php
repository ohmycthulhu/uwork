<?php

namespace App\Models\User;

use App\Models\Categories\Category;
use App\Models\Profile\ProfileView;
use App\Models\Profile\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Profile extends Model implements HasMedia
{
  use SoftDeletes, HasMediaTrait;

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

  /**
   * Methods
   */

  /**
   * Method to synchronize rating and reviews count
   *
   * @return $this
  */
  public function synchronizeReviews(): Profile {
    $reviewsCount = $this->reviews()->count();
    if ($reviewsCount != $this->reviews_count) {
      $this->reviews_count = $reviewsCount;
      $this->rating = $reviewsCount > 0 ? ($this->reviews()->sum('rating') / $reviewsCount) : 0;

      $this->save();
    }

    return $this;
  }

  /**
   * Method to synchronize views
   *
   * @return $this
  */
  public function synchronizeViews(): Profile {
    $viewsCount = $this->views()->count();
    $openCount = $this->views()->open()->count();
    if ($viewsCount != $this->views_count || $openCount != $this->open_count) {
      $this->views_count = $viewsCount;
      $this->open_count = $openCount;
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
   * Method to add specialities
   *
   * @param int $categoryId
   * @param float $price
   *
   * @return Model
   */
  public function addSpeciality(int $categoryId, float $price): Model
  {
    return $this->specialities()
      ->create(['category_id' => $categoryId, 'price' => $price]);
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
   * Method to change avatar
   *
   * @param UploadedFile $image
   *
   * @return $this
   */
  public function setAvatar(UploadedFile $image): Profile
  {
    $fileName = Str::random() . ".{$image->getClientOriginalExtension()}";

    try {
      Storage::disk('public')
        ->put("avatars/$fileName", \Illuminate\Support\Facades\File::get($image));
    } catch (\Exception $e) {
      throw $e;
    }

    $this->picture = "avatars/$fileName";
    $this->save();

    return $this;
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
  public function reviews(): HasMany {
    return $this->hasMany(Review::class, 'profile_id');
  }

  /**
   * Relation to views
   *
   * @return HasMany
  */
  public function views(): HasMany {
    return $this->hasMany(ProfileView::class, 'profile_id');
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
  public function scopePublic(Builder $query): Builder {
    return $query->whereNotNull('verified_at')
      ->where('failed_audition', false)
      ->visible();
  }

  /**
   * Scope region
   *
   * @param Builder $query
   * @param int $regionId
   *
   * @return Builder
  */
  public function scopeRegion(Builder $query, int $regionId): Builder {
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
  public function scopeCity(Builder $query, int $cityId): Builder {
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
  public function scopeDistrict(Builder $query, int $districtId): Builder {
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
  public function scopeExactCategory(Builder $query, Category $category): Builder {
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
  public function scopeCategory(Builder $query, int $categoryId): Builder {
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
  public function scopeNotUser(Builder $query, User $user): Builder {
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
  public function getIsApprovedAttribute(): bool {
    return !!$this->verified_at && !$this->failed_audition;
  }
}
