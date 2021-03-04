<?php

namespace App\Nova\Filters;

use App\Models\Categories\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class CategoriesFilter extends Filter
{
  /**
   * The filter's component.
   *
   * @var string
   */
  public $component = 'select-filter';

  /**
   * Apply the filter to the given query.
   *
   * @param \Illuminate\Http\Request $request
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param mixed $value
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function apply(Request $request, $query, $value): Builder
  {
    if ($value == 'null') {
      $query->top();
    } else {
      $query->parent($value);
    }
    return $query;
  }

  public function default(): string
  {
    return 'null';
  }

  /**
   * Get the filter's available options.
   *
   * @param \Illuminate\Http\Request $request
   * @return array
   */
  public function options(Request $request): array
  {
    return array_merge(['Top' => 'null'],
      Category::all()
      ->pluck('id', 'name')
      ->toArray()
    );
  }
}
