<?php

namespace App\Nova\Resources\Location;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class Region extends LocationResource
{
  /**
   * The model the resource corresponds to.
   *
   * @var string
   */
  public static $model = \App\Models\Location\Region::class;

  /**
   * Get the fields displayed by the resource.
   *
   * @param \Illuminate\Http\Request $request
   * @return array
   */
  public function fields(Request $request)
  {
    return [
      ID::make(__('ID'), 'id')->sortable(),
      Text::make(__('Name'), 'name')
        ->translatable()->sortable(),
      Text::make(__('Place ID'), 'google_id')
        ->nullable()->sortable(),
      HasMany::make(__('Cities'), 'cities', City::class)
    ];
  }
}
