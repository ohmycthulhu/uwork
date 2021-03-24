<?php

namespace App\Models\Complaints;

use App\Models\Scopes\OrderScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use SoftDeletes;

    protected $fillable = ['text', 'reason_other', 'type_id', 'ip_addr', 'user_id'];

    protected static function boot()
    {
      parent::boot();
      static::addGlobalScope(new OrderScope('created_at', 'desc'));
    }

  /**
     * Relation to user
     *
     * @return BelongsTo
    */
    public function user(): BelongsTo {
      return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Morph relation
     *
     * @return MorphTo
    */
    public function complaintable(): MorphTo {
      return $this->morphTo();
    }

    /**
     * Relation to type
     *
     * @return BelongsTo
    */
    public function type(): BelongsTo {
      return $this->belongsTo(ComplaintType::class, 'type_id');
    }

    /**
     * Scope to filter by open or closed
     *
     * @param Builder $query
     * @param bool $state
     *
     * @return Builder
    */
    public function scopeState(Builder $query, bool $state): Builder {
      return $query->where('is_open', $state);
    }

    /**
     * Scope similar
     *
     * @param Builder $query
     * @param ?User $user
     * @param ?string $ip
     *
     * @return Builder
    */
    public function scopeSimilar(Builder $query, ?User $user, ?string $ip): Builder {
      return $query->where(function ($q) use ($user, $ip){
        if ($user) {
          $q->orWhere('user_id', $user->id);
        }
        if ($ip) {
          $q->orWhere('ip_addr', $ip);
        }
      });
    }
}
