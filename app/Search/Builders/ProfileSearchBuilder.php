<?php


namespace App\Search\Builders;

use App\Models\User\Profile;
use App\Search\Results\ProfileSearchResult;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;

/**
 * Class for performing complex search on profiles
 *
 */
class ProfileSearchBuilder
{
  const SORT_PRICE = 'price_avg';
  const SORT_DISTRICT = 'district';
  const SORT_RATING = 'rating';

  /* @var BoolQueryBuilder $queryBuilder */
  protected $queryBuilder;

  /**
   * Instantiates an object
   *
   * @param Profile $model
   */
  public function __construct(Profile $model)
  {
    $this->queryBuilder = $model::boolSearch();

    $this->queryBuilder->must(['match' => ['isConfirmed' => "1"]]);
  }

  /**
   * Sets a location information
   *
   * @param ?int $regionId
   * @param ?int $cityId
   * @param ?int $districtId
   * @param ?int $subwayId
   *
   * @return self
   */
  public function setLocation(?int $regionId, ?int $cityId, ?int $districtId, ?int $subwayId): self
  {
    $locations = [];
    /* Prepare location constraints */
    if ($subwayId) {
      $locations['subwayId'] = $subwayId;
    } elseif ($districtId) {
      $locations["districtId"] = $districtId;
    } elseif ($cityId) {
      $locations["cityId"] = $cityId;
    } elseif ($regionId) {
      $locations["regionId"] = $regionId;
    }
    if ($locations) {
      $this->queryBuilder->must(["match" => $locations]);
    }


    return $this;
  }

  /**
   * Sets price range
   *
   * @param ?float $min
   * @param ?float $max
   *
   * @return self
   */
  public function setPriceRange(?float $min, ?float $max): self
  {
    $range = [];
    if ($min != null) $range['gte'] = $min;
    if ($max != null) $range['lte'] = $max;
    if ($range) {
      $this->queryBuilder->must([
        'range' => [
          'specialities.price' => $range
        ]
      ]);
    }
    return $this;
  }

  /**
   * Filters out the current user
   *
   * @param int $userId
   *
   * @return self
   */
  public function setCurrentUser(int $userId): self
  {
    $this->queryBuilder->mustNot([
      'match' => [
        'user_id' => $userId,
      ]
    ]);
    return $this;
  }

  /**
   * Filters out by categories
   *
   * @param ?int $parentCategoryId
   * @param array $categories
   *
   * @return self
   */
  public function setCategories(array $categories, ?int $parentCategoryId = null): self
  {
    if ($parentCategoryId) {
      $this->queryBuilder->must([
        "match" => ["specialities.catPath" => $parentCategoryId]
      ]);
    }
    if ($categories) {
      foreach ($categories as $cat) {
        $this->queryBuilder->should([
          "match" => ["specialities.categoryId" => $cat]
        ]);
      }
      $this->queryBuilder->minimumShouldMatch(1);
    }
    return $this;
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
   * Sets sorting
   *
   * @param string $column
   * @param string $dir
   *
   * @return self
   */
  public function setSorting(string $column, string $dir): self
  {
    $col = $this->getSortingColumn($column);

    if ($col) {
      $this->queryBuilder->sort($col, $dir);
    }

    return $this;
  }

  /**
   * Get column for sorting
   *
   * @param string $column
   *
   * @return ?string
  */
  protected function getSortingColumn(string $column): ?string {
    switch (strtolower($column)) {
      case 'price': return static::SORT_PRICE;
      case 'district': return static::SORT_DISTRICT;
      case 'rating': return static::SORT_RATING;
      default: return null;
    }
  }

  /**
   * Execute the query
   *
   * @return ProfileSearchResult
   */
  public function execute(): ProfileSearchResult
  {
    $result = $this->queryBuilder->execute();

    return new ProfileSearchResult($result->models(), $result->total());
  }
}