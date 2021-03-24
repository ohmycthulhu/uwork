<?php

namespace App\Models\User;

use App\Models\Scopes\OrderScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Notification extends Model
{
    use SoftDeletes, HasTranslations;

    protected $fillable = [
      'user_id', 'notifiable_id', 'notifiable_type',
      'title', 'description', 'read_at',
    ];

    public $translatable = [
      'title', 'description',
    ];

    protected $appends = ['isRead'];

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
     * Morphing relation
     *
     * @return MorphTo
    */
    public function notifiable(): MorphTo {
      return $this->morphTo();
    }

    /**
     * Scope by unread
     *
     * @param Builder $query
     *
     * @return Builder
    */
    public function scopeUnread(Builder $query): Builder {
      return $query->whereNull('read_at');
    }

    /**
     * Scope by array of ids
     *
     * @param Builder $query
     * @param array $ids
     *
     * @return Builder
    */
    public function scopeIds(Builder $query, array $ids): Builder {
      return $query->whereIn('id', $ids);
    }

    /**
     * Attribute to check if it's already read or not
     *
     * @return bool
    */
    public function getIsReadAttribute(): bool {
      return !$this->read_at;
    }
}
