<?php

namespace App\Models\Search;

use ElasticScoutDriverPlus\CustomSearch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;

class SearchHistory extends Model
{
  use Searchable, CustomSearch;
    protected $fillable = [
      'text', 'weight'
    ];

  /**
   * Method to search similar categories
   *
   * @param string $name
   * @param int $count
   *
   * @return Collection
   */
  public static function searchAutocomplete(string $name, int $count = 10): Collection {
    return static::boolSearch()
      ->should(['wildcard' => ['text' => ['value' => "*$name*"]]])
      ->should(['match' => ['text' => ['query' => $name]]])
      ->minimumShouldMatch(1)
      ->size($count)
      ->sort('weight', 'desc')
      ->execute()
      ->models();
  }

  public function toSearchableArray(): array
  {
    return [
      'text' => $this->text,
      'weight' => $this->weight,
    ];
  }

  /**
     * Scope to search by text
     *
     * @param Builder $query
     * @param string $text
     *
     * @return Builder
    */
    public function scopeText(Builder $query, string $text): Builder {
      return $query->where('text', 'like', $text);
    }

}
