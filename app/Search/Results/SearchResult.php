<?php

namespace App\Search\Results;

use Illuminate\Support\Collection;

/**
 * Class for storing the result of search query
*/
class SearchResult
{
  /* @var Collection $models */
  protected $models;
  /* @var int $total */
  protected $total;

  /**
   * Instantiates the object
   *
   * @param Collection $models
   * @param int $total
  */
  public function __construct(Collection $models, int $total)
  {
    $this->models = $models;
    $this->total = $total;
  }

  /**
   * Get the total number
   *
   * @return int
   */
  public function getTotal(): int {
    return $this->total;
  }

  /**
   * Get the amount on the current page
   *
   * @return int
   */
  public function getCount(): int {
    return $this->models->count();
  }

  /**
   * Get the result
   *
   * @return Collection
   */
  public function getModels(): Collection {
    return $this->models;
  }
}