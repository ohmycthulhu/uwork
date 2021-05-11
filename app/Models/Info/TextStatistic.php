<?php

namespace App\Models\Info;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TextStatistic extends Model
{
  protected $fillable = [
    'key', 'upvotes', 'downvotes'
  ];

  /**
   * Method to increment statistic by type
   *
   * @param string $type
   *
   * @return int
  */
  public static function incrementByType(string $type): int {
    $textStatistic = static::getOrCreate($type);
    $textStatistic->upvotes += 1;
    $textStatistic->save();
    return $textStatistic->upvotes;
  }

  /**
   * Method to decrement statistic by type
   *
   * @param string $type
   *
   * @return int
  */
  public static function decrementByType(string $type): int {
    $textStatistic = static::getOrCreate($type);
    $textStatistic->downvotes += 1;
    $textStatistic->save();
    return $textStatistic->downvotes;
  }

  /**
   * Function to get or create model
   *
   * @param string $type
   *
   * @return TextStatistic
  */
  protected static function getOrCreate(string $type): TextStatistic {
    return static::query()
      ->type($type)
      ->first() ?? static::create(['key' => $type]);
  }

  /**
   * Method to get statistic by type
   *
   * @param Builder $query
   * @param string $type
   *
   * @return Builder
  */
  public static function scopeType(Builder $query, string $type): Builder {
    return $query->where('key', $type);
  }

  /**
   * Attribute to get total votes
   *
   * @return int
  */
  public function getTotalAttribute(): int {
    return $this->upvotes - $this->downvotes;
  }
}
