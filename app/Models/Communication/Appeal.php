<?php

namespace App\Models\Communication;

use App\Models\Scopes\OrderScope;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appeal extends Model
{
    use SoftDeletes;

    protected $fillable =[
      'name', 'text', 'appeal_reason_id', 'appeal_reason_other',
      'ip_addr', 'email', 'phone', 'user_id',
    ];

    protected static $throttleDuration = 240;
    protected static $throttleAmount = 3;

    public function __construct(array $attributes = [])
    {
      parent::__construct($attributes);
      self::addGlobalScope(new OrderScope("created_at", 'desc'));
    }

  /**
     * Methods
    */

    /**
     * Static method to create an appeal
     *
     * @param string $text
     * @param ?int $appealReason
     * @param ?string $reasonOther
     *
     * @param ?User $user
     * @param ?string $name
     * @param ?string $ipAddr
     * @param ?string $phone
     * @param ?string $email
     *
     * @throws Exception
     *
     * @return Appeal
    */
    public static function instantiate(
      string $text,
      ?int $appealReason,
      ?string $reasonOther,

      ?User $user,
      ?string $name,
      ?string $ipAddr,
      ?string $phone,
      ?string $email
    ): Appeal {
      // Check if there are enough similar requests
      if (
        static::query()
          ->similar($phone, $email, $ipAddr, $user ? $user->id : null)
          ->time(static::$throttleDuration)
          ->count() >= static::$throttleAmount
      ) {
        // Throw an error
        throw new Exception("Too many appeals");
      }

      // Create and return an appeal
      return Appeal::create([
        'name' => $name ?? ($user ? $user->name : ''),
        'text' => $text,
        'appeal_reason_id' => $appealReason,
        'appeal_reason_other' => $reasonOther,

        'ip_addr' => $ipAddr,
        'email' => $email ? $email : ($user ? $user->email : null),
        'phone' => $phone ? $phone : ($user ? $user->phone : null),
        'user_id' => $user ? $user->id : null
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
    public function user(): BelongsTo {
      return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation to reason
     *
     * @return BelongsTo
    */
    public function reason(): BelongsTo {
      return $this->belongsTo(AppealReason::class, 'appeal_reason_id');
    }

    /**
     * Scopes
    */

    /**
     * Scope to filter similar appeals
     *
     * @param Builder $query
     * @param ?string $phone
     * @param ?string $email
     * @param ?string $ip
     * @param ?int $userId
     *
     * @return Builder
    */
    public function scopeSimilar(Builder $query,
                                 ?string $phone,
                                 ?string $email,
                                 ?string $ip,
                                 ?int $userId): Builder {
      return $query->where(function (Builder $query) use ($phone, $email, $ip, $userId) {
        // Scope by whatever is presented
        if ($phone) {
          $query->orWhere('phone', $phone);
        }
        if ($email) {
          $query->orWhere('email', $email);
        }
        if ($ip) {
          $query->orWhere('ip_addr', $ip);
        }
        if ($userId) {
          $query->orWhere('user_id', $userId);
        }
      });
    }

    /**
     * Scope to filter by time
     *
     * @param Builder $query
     * @param int $minutes
     *
     * @return Builder
    */
    public function scopeTime(Builder $query, int $minutes): Builder {
      return $query->where(
        'created_at',
        '>=',
        now()->addMinutes(-$minutes)->format('Y-m-d')
      );
    }
}
