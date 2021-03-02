<?php

namespace App\Models\Messenger;

use App\Models\Scopes\OrderScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class Message extends Model
{
  use Searchable;

  protected $fillable = [
    'text', 'attachment', 'user_id',
  ];

  public function toSearchableArray(): array
  {
    return [
      'id' => $this->id,
      'text' => $this->text,
      'chat_id' => $this->chat_id,
    ];
  }

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
   * Relation to chat
   *
   * @return BelongsTo
   */
  public function chat(): BelongsTo {
    return $this->belongsTo(Chat::class, 'chat_id');
  }

  /**
   * Scope by chat
   *
   * @param Builder $query
   * @param Chat $chat
   *
   * @return Builder
  */
  public function scopeChat(Builder $query, Chat $chat): Builder {
    return $query->where('chat_id', $chat->id);
  }
}
