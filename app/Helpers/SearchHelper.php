<?php


namespace App\Helpers;

use App\Models\Categories\Category;
use App\Models\Search\SearchHistory;
use App\Utils\CacheAccessor;
use Illuminate\Support\Collection;

/**
 * Class to encapsulate all work with search
*/
class SearchHelper
{
  /**
   * Instance of search history
   * @var SearchHistory
  */
  protected $searchHistory;

  /**
   * Cache key for intermediate storing keys
   *
   * @var string
  */
  protected $cacheKey;

  /**
   * Cache storage
   *
   * @var CacheAccessor
  */
  protected $store;

  /**
   * Create instance of helper
   *
   * @param SearchHistory $searchHistory
   * @param ?string $cacheKey
  */
  public function __construct(SearchHistory $searchHistory, ?string $cacheKey = null)
  {
    $this->searchHistory = $searchHistory;
    $this->store = new CacheAccessor($cacheKey ?? "search-history", [], 1440);
  }

  /**
   * Function to register search
   *
   * @param string $text
   *
   * @return bool
  */
  public function registerSearch(string $text): bool {
    $keywords = $this->store->get('keywords');
    array_push($keywords, $text);
    $this->store->set('keywords', $keywords);
    return false;
  }

  /**
   * Function to search for autocomplete
   *
   * @param string $text
   *
   * @return Collection
  */
  public function getAutocomplete(string $text): Collection {
    $models = $this->searchHistory::searchAutocomplete($text);

    return $models->map(function (SearchHistory $sh) { return $sh->text; });
  }

  /**
   * Function to perform weight decrease
  */
  public function optimizeStorage() {
    $keywordsCount = $this->searchHistory::query()->count();
    $processed = 0;
    $step = 1000;
    while ($processed < $keywordsCount) {
      $keywords = $this->searchHistory::query()
        ->skip($processed)
        ->take($step)
        ->get();

      foreach ($keywords as $keyword) {
        $weight = floor($keyword->weight / 2);
        if ($weight > 0) {
          // Update weight
          $keyword->weight = $weight;
          $keyword->save();
        } else {
          // Delete the element
          $keyword->delete();
        }
      }

      $processed += $step;
    }
  }

  /**
   * Function to import all keywords
   *
   * @return int
  */
  public function importKeywords(): int {
    $keywords = $this->store->get('keywords');
    $this->store->remove('keywords');

    $frequency = $this->getFrequency($keywords);

    foreach ($frequency as $keyword => $count) {
      $searchHistory = $this->searchHistory::query()->text($keyword)->first();
      if ($searchHistory) {
        /* Increment the weight */
        $searchHistory->weight += $count;
        $searchHistory->save();
      } else {
        /* Create keyword */
        $this->searchHistory::create(['text' => $keyword, 'weight' => $count]);
      }
    }

    return sizeof($keywords);
  }

  /**
   * Function to sort keywords by occurrence
   *
   * @param array $arr
   *
   * @return array
  */
  protected function getFrequency(array $arr): array {
    $result = [];
    foreach ($arr as $element) {
      $result[$element] = ($result[$element] ?? 0) + 1;
    }
    return $result;
  }

  /**
   * Method to calculate category path
   *
   * @param int $categoryId
   *
   * @return string
   */
  public function calculateCategoryPath(int $categoryId): string {
    /* @var ?Category $category */
    $category = Category::query()->find($categoryId);
    if (!$category) {
      return '';
    }
    $lastCategoryId = $categoryId;
    $result = '';
    do {
      $result = "f{$lastCategoryId}c{$result}";
      $category = $category->parent()->first();
      $lastCategoryId = $category ? $category->id : null;
    } while ($lastCategoryId);

    return "s{$result}e";
  }
}