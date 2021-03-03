<?php

namespace App\Models\Profile;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'headline', 'text', 'user_id', 'profile_id', 'speciality_id',
    'ip_addr', 'rating_quality', 'rating_time', 'rating_price'
  ];

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
}
