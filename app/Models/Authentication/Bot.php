<?php

namespace App\Models\Authentication;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bot extends Model
{
    use SoftDeletes;

    protected $fillable = [
      'token', 'name', 'enabled'
    ];

    /**
     * Method to change bot status
     *
     * @param bool $state
     *
     * @return $this
    */
    public function setState(bool $state): self {
      $this->enabled = $state;
      $this->save();
      return $this;
    }

    /**
     * Method to create bot
     *
     * @param string $token
     * @param ?string $name
     * @param bool $state
     *
     * @return Bot
    */
    public static function createBot(string $token, ?string $name = null, bool $state = true) {
      $bot = static::query()->withTrashed()->token($token)->first();

      if ($bot) {
        $bot->restore();
        $bot->name = $name;
        $bot->enabled = $state;
        $bot->save();
      } else {
        $bot = static::create([
          'token' => $token,
          'name' => $name,
          'enabled' => $state,
        ]);
      }
      return $bot;
    }

    /**
     * Method to scope by token
     *
     * @param Builder $query
     * @param string  $token
     *
     * @return Builder
    */
    public function scopeToken(Builder $query, string $token): Builder {
      return $query->where('token', $token);
    }

    /**
     * Method to scope by enable status
     *
     * @param Builder $query
     * @param bool $state
     *
     * @return Builder
    */
    public function scopeState(Builder $query, bool $state): Builder {
      return $query->where('enabled', $state);
    }
}
