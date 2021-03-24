<?php

namespace App\Models\Profile;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Review extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'headline', 'text', 'user_id', 'profile_id', 'speciality_id',
    'ip_addr', 'rating_quality', 'rating_time', 'rating_price'
  ];

  /**
   * Method to reply to reviews
   *
   * @param int $userId
   * @param ?string $ip
   * @param string $headline
   * @param string $text
   *
   * @return Review
  */
  public function reply(int $userId, ?string $ip, string $headline, string $text): Review {
    return $this->replies()
      ->create([
        'user_id' => $userId,
        'headline' => $headline,
        'text' => $text,
        'profile_id' => $this->profile_id,
        'speciality_id' => $this->speciality_id,
        'ip_addr' => $ip,
      ]);
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
   * Relation to profile
   *
   * @return BelongsTo
   */
  public function profile(): BelongsTo
  {
    return $this->belongsTo(User\Profile::class, 'profile_id');
  }

  /**
   * Relation to speciality
   *
   * @return BelongsTo
   */
  public function speciality(): BelongsTo
  {
    return $this->belongsTo(User\ProfileSpeciality::class, 'speciality_id');
  }

  /**
   * Relation to replies
   *
   * @return HasMany
  */
  public function replies(): HasMany {
    return $this->hasMany(Review::class, 'parent_id');
  }

  /**
   * Relation to parent
   *
   * @return BelongsTo
  */
  public function parent(): BelongsTo {
    return $this->belongsTo(Review::class, 'parent_id');
  }

  /**
   * Scopes
   */

  /**
   * Scope by user
   *
   * @param Builder $query
   * @param int $userId
   *
   * @return Builder
   */
  public function scopeUserId(Builder $query, int $userId): Builder
  {
    return $query->where('user_id', $userId);
  }

  /**
   * Scope by ip address
   *
   * @param Builder $query
   * @param string $ip
   *
   * @return Builder
   */
  public function scopeIpAddr(Builder $query, string $ip): Builder
  {
    return $query->where('ip_addr', $ip);
  }

  /**
   * Scope by speciality
   *
   * @param Builder $query
   * @param int $specialityId
   *
   * @return Builder
   */
  public function scopeSpecialityId(Builder $query, int $specialityId): Builder
  {
    return $query->where('speciality_id', $specialityId);
  }

  /**
   * Scope by having ratings
   *
   * @param Builder $query
   *
   * @return Builder
  */
  public function scopeHasRatings(Builder $query): Builder {
    return $query->whereNotNull('rating_quality')
      ->whereNotNull('rating_price')
      ->whereNotNull('rating_time');
  }

  /**
   * Scope by top
   *
   * @param Builder $query
   *
   * @return Builder
  */
  public function scopeTop(Builder $query): Builder {
    return $query->whereNull('parent_id');
  }

  /**
   * Scope to count by specialities
   *
   * @param Builder $query
   *
   * @return Builder
  */
  public function scopeSpecialitiesCount(Builder $query): Builder {
    return $query->groupBy('speciality_id')
      ->select("speciality_id", DB::raw("COUNT(*) AS `total`"));
  }

  /**
   * Scope for date
   *
   * @param Builder $query
   * @param int $hours
   *
   * @return Builder
  */
  public function scopeLastHours(Builder $query, int $hours): Builder {
    return $query->where('created_at', '>=', now()->addHours(-$hours));
  }

  /**
   * Scope for average rating
   *
   * @param Builder $query
   * @param float $rating
   *
   * @return Builder
  */
  public function scopeAverageRating(Builder $query, float $rating): Builder {
    $totalRating = $rating * 3;
    return $query->whereRaw("(rating_price + rating_time + rating_quality) >= $totalRating");
  }
}
