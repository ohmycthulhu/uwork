<?php


namespace App\Search\Builders;


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
   * @return self
   */
  public function setPagination(int $pageSize, int $pageNumber): self
  {
    $this->queryBuilder
      ->paginate($pageSize, 'page', $pageNumber);
    return $this;
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
}