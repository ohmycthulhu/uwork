<?php

namespace App\Nova\Resources\Location;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class City extends LocationResource
{
  /**
   * The model the resource corresponds to.
   *
   * @var string
   */
  public static $model = \App\Models\Location\City::class;

  /**
   * Get the fields displayed by the resource.
   *
   * @param Request $request
   * @return array
   */
  public function fields(Request $request): array
  {
    return [
      ID::make(__('ID'), 'id')->sortable(),

      BelongsTo::make(__('Region'), 'region', Region::class)
        ->sortable(),
      Text::make(__('Name'), 'name')
        ->sortable(),
      Text::make(__('Place ID'), 'google_id')
        ->nullable()->sortable(),
      HasMany::make(__('Districts'), 'districts', District::class)
    ];
  }
}
