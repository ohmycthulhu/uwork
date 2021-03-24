<?php

namespace App\Nova\Filters\Complaints;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class StateFilter extends Filter
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
  public function apply(Request $request, $query, $value)
  {
    if ($value === 'open') {
      $query->state(true);
    } elseif ($value === 'closed') {
      $query->state(false);
    }
    return $query;
  }

  /**
   * Get the filter's available options.
   *
   * @param \Illuminate\Http\Request $request
   * @return array
   */
  public function options(Request $request)
  {
    return [
      'Open' => 'open',
      'Closed' => 'closed',
    ];
  }
}
