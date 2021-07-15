<?php


namespace App\Search\Builders;


use App\Models\User\Profile;
use App\Search\Results\SearchResult;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;

abstract class SearchBuilder
{
  /* @var BoolQueryBuilder $queryBuilder */
  protected $queryBuilder;

  /**
   * Instantiates an object
   *
   * @param $model
   */
  public function __construct($model)
  {
    $this->queryBuilder = $model::boolSearch();
  }

  /**
   * Sets pagination configuration
   *
   * @param int $pageNumber
   * @param int $pageSize
   *
   * @return SearchResult
   */
  public function paginate(int $pageSize, int $pageNumber): SearchResult
  {
    $result = $this->queryBuilder
      ->paginate($pageSize, 'page', $pageNumber);

    return new SearchResult($result->models(), $result->total());
  }

  /**
   * Set the size limit
   *
   * @param int $size
   *
   * @return self
  */
  public function setSize(int $size): self {
    $this->queryBuilder->size($size);
    return $this;
  }

  /**
   * Execute the query
   *
   * @return SearchResult
   */
  public function execute(): SearchResult
  {
    $result = $this->queryBuilder->execute();

    return new SearchResult($result->models(), $result->total());
  }

  /**
   * Set array search
   *
   * @param string $field
   * @param array $values
   *
   * @return $this
   */
  protected function setArraySearch(string $field, array $values): self {
    $this->queryBuilder->must(['terms' => [$field => $values]]);
    return $this;
  }
}