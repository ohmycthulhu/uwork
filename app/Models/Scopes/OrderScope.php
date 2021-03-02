<?php


namespace App\Models\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrderScope implements Scope
{
  /**
   * Column to order
   * @var string
   */
  protected $column;

  /**
   * Order direction
   *
   * @var string
  */
  protected $dir;

  /**
   * Creates instance of order
   *
   * @param string $column
   * @param string $direction
  */
  public function __construct(string $column, string $direction = 'asc')
  {
    $this->column = $column;
    $this->dir = $direction;
  }

  /**
   * Apply the scope to a given Eloquent query builder
   *
   * @param Builder $builder
   * @param Model $model
   *
   * @return void
  */
  public function apply(Builder $builder, Model $model)
  {
    $builder->orderBy($this->column, $this->dir);
  }
}