<?php

namespace App\Nova\Filters\Profiles;

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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
      if ($value === 'rejected')
        $query->where('is_rejected', true);
      elseif ($value === 'confirmed')
        $query->whereNotNull('confirmed_at');
      elseif ($value === 'pending')
        $query->where('is_rejected', false)
          ->whereNull('confirmed_at');

        return $query;
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
          'Rejected' => 'rejected',
          'Confirmed' => 'confirmed',
          'Pending' => 'pending',
        ];
    }
}
