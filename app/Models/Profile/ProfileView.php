<?php

namespace App\Models\Profile;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileView extends Model
{
  // Fillables
  protected $fillable = [
    'profile_id', 'user_id', 'ip_addr', 'has_open',
  ];

  /**
   * Factory method to create view
   *
   * @param User\Profile $profile
   * @param ?User $user
   * @param ?string $ip
   * @param bool $opened
   *
   * @return ?ProfileView
   */
  public static function make(User\Profile $profile, ?User $user, ?string $ip, bool $opened = false): ?ProfileView
  {
    if (!$user && !$ip) {
      return null;
    }

    // Check if similar view exists
    $query = $profile->views()->similar($user, $ip);

    $view = $query->first();

    if (!$view) {
      $view = $profile->views()->create([
        'user_id' => $user ? $user->id : null,
        'ip_addr' => $ip,
        'opened' => $opened,
      ]);
    } elseif (!$view->opened && $opened) {
      $view->opened = $opened;
      $view->save();
    }

    return $view;
  }

  /*
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

  /*
   * Scopes
  */

  /**
   * Scope by user
   *
   * @param Builder $query
   * @param User $user
   *
   * @return Builder
   */
  public function scopeUser(Builder $query, User $user): Builder
  {
    return $query->where('user_id', $user->id);
  }

  /**
   * Scope by ip address
   *
   * @param Builder $query
   * @param string $ip
   *
   * @return Builder
   */
  public function scopeIp(Builder $query, string $ip): Builder
  {
    return $query->where('ip_addr', 'LIKE', $ip);
  }

  /**
   * Scope by similar by ip and user
   *
   * @param Builder $query
   * @param ?User $user
   * @param ?string $ip
   *
   * @return Builder
   */
  public function scopeSimilar(Builder $query, ?User $user, ?string $ip): Builder
  {
    return $query->where(function ($q) use ($user, $ip) {
      if ($user) {
        $q->where('user_id', $user->id);
      }
      if ($ip) {
        $q->orWhere('ip_addr', 'like', $ip);
      }
      return $q;
    });
  }

  /**
   * Scope by opened
   *
   * @param Builder $query
   *
   * @return Builder
  */
  public function scopeOpen(Builder $query): Builder {
    return $query->where('opened', true);
  }
}
