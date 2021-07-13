<?php


namespace App\Search\Builders;

use App\Models\User\Profile;

/**
 * Class for performing complex search on profiles
 *
 */
class ProfileSearchBuilder extends SearchBuilder
{
  const SORT_PRICE = 'price_avg';
  const SORT_DISTRICT = 'district';
  const SORT_RATING = 'rating';

  /**
   * Instantiates an object
   *
   * @param Profile $model
   */
  public function __construct(Profile $model)
  {
    parent::__construct($model);

    $this->queryBuilder->must(['match' => ['isConfirmed' => "1"]]);
  }

  /**
   * Sets a location information
   *
   * @param ?int $regionId
   * @param ?int $cityId
   * @param int | array | null $districtId
   * @param int | array | null $subwayId
   *
   * @return self
   */
  public function setLocation(?int $regionId, ?int $cityId, $districtId, $subwayId): self
  {
    $locations = [];
    /* Prepare location constraints */
    if ($subwayId) {
      if (is_array($subwayId)) {
        $this->setArraySearch('subwayId', $subwayId);
      } else {
        $locations['subwayId'] = $subwayId;
      }
    } elseif ($districtId) {
      if (is_array($districtId)) {
        $this->setArraySearch('districtId', $districtId);
      } else  {
        $locations["districtId"] = $districtId;
      }
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
   * Set rating range
   *
   * @param ?float $ratingMin
   * @param ?float $ratingMax
   *
   * @return self
  */
  public function setRatingRange(?float $ratingMin, ?float $ratingMax): self {
    if ($query = $this->getRatingRange($ratingMin, $ratingMax)) {
      $this->queryBuilder->must($query);
    }
    return $this;
  }

  /**
   * Set ratings ranges
   *
   * @param array $ratingRanges
   *
   * @return self
  */
  public function setRatingRanges(array $ratingRanges): self {
    $queries = [];
    foreach ($ratingRanges as $range) {
      if ($query = $this->getRatingRange($range['min'] ?? null, $range['max'] ?? null)) {
        $queries[] = $query;
      }
    }
    $this->queryBuilder->must(['bool' => ['should' => $queries]]);
    return $this;
  }

  /**
   * Set rating range
   *
   * @param ?float $min
   * @param ?float $max
   *
   * @return ?array
  */
  protected function getRatingRange(?float $min, ?float $max): ?array {
    $range = [];
    if ($min != null) $range['gte'] = $min;
    if ($max != null) $range['lte'] = $max;
    if ($range) {
      return ['range' => ['rating' => $range]];
    }
    return null;
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