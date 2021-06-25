<?php


namespace App\Search\Builders;


use Illuminate\Support\Str;

class CategoriesSearchBuilder extends SearchBuilder
{
  /**
   * Method to set the name
   *
   * @param string $name
   *
   * @return self
   */
  public function setName(string $name): self
  {
    $keyword = str_replace(
      " ",
      "*",
      Str::lower(trim($name))
    );

    if ($keyword) {
      $this->queryBuilder
        ->should(['wildcard' => ['name' => "*$keyword*"]])
        ->minimumShouldMatch(1);
    }
    return $this;
  }


  /**
   * Method to set the parent id
   *
   * @param int $parentId
   *
   * @return self
   */
  public function setParentId(int $parentId): self
  {
    $this->queryBuilder->must(['match' => ['category_path' => $parentId]]);
    return $this;
  }

  /**
   * Set the size of chunk
   *
   * @param
  */
}