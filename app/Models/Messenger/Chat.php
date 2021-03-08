<?php

namespace App\Models\Messenger;

use App\Models\Scopes\OrderScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Chat extends Model
{
    use SoftDeletes;

    protected $fillable = [
      'initiator_id', 'acceptor_id',
      'last_message_time'
    ];

    protected $casts = [
      'last_message_time' => 'datetime'
    ];

    protected static function boot()
    {
      parent::boot();
      self::addGlobalScope(new OrderScope('last_message_time', 'desc'));
    }

    /**
     * Function to mark messages as read
     *
     * @param User $user
     *
     * @return int
    */
    public function markAsRead(User $user): int {
      $query = $this->messages()->unread($user);
      return $query->update(['read_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Factory to create new chat
     *
     * @param User $initiator
     * @param User $acceptor
     *
     * @return Chat
    */
    public static function make(User $initiator, User $acceptor): Chat {
      return static::create([
        'initiator_id' => $initiator->id,
        'acceptor_id' => $acceptor->id,
      ]);
    }

    /**
     * Send message
     *
     * @param ?string $text
     * @param ?UploadedFile $attachment
     *
     * @return Message
    */
    public function sendMessage(?string $text, ?UploadedFile $attachment): Message {
      $attachmentPath = null;

      if ($attachment) {
        $fileName = Str::random(32).".{$attachment->extension()}";
        $path = "messages";
        $attachment->move(storage_path("app/public/$path"), $fileName);
        $attachmentPath = "storage/$path/$fileName";
      }

      return $this->messages()->create([
        'text' => $text,
        'attachment' => $attachmentPath,
      ]);
    }

    /**
     * Method to set last message time
     *
     * @param Message $message
     *
     * @return $this
     */
    public function setLastMessage(Message $message): Chat {
      if (!$this->last_message_time || $this->last_message_time < $message->created_at) {
        $this->last_message_time = $message->created_at;
        $this->save();
      }

      return $this;
    }

    /**
     * Relation to messages
     *
     * @return HasMany
    */
    public function messages(): HasMany {
      return $this->hasMany(Message::class, 'chat_id');
    }

    /**
     * Relation to users
    */

    /**
     * Relation to initiator user
     *
     * @return BelongsTo
    */
    public function initiator(): BelongsTo {
      return $this->belongsTo(User::class, 'initiator_id');
    }

    /**
     * Relation to acceptor user
     *
     * @return BelongsTo
    */
    public function acceptor(): BelongsTo {
      return $this->belongsTo(User::class, 'acceptor_id');
    }

    /**
     * Relation to unread messages
     *
     * @return HasMany
    */
    public function unreadMessages(): HasMany {
      return $this->messages()
        ->unread(Auth::user());
    }

    /**
     * Scope to filter by user
     *
     * @param Builder $query
     * @param User $user
     *
     * @return Builder
    */
    public function scopeUser(Builder $query, User $user): Builder {
      return $query->where(function (Builder $q) use ($user) {
        return $q->where('initiator_id', $user->id)
          ->orWhere('acceptor_id', $user->id);
      });
    }
}
