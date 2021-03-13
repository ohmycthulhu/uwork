<?php

namespace App\Nova\Resources\Location;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class District extends LocationResource
{
  /**
   * The model the resource corresponds to.
   *
   * @var string
   */
  public static $model = \App\Models\Location\District::class;

  /**
   * Get the fields displayed by the resource.
   *
   * @param Request $request
   * @return array
   */
  public function fields(Request $request): array
  {
    return [
      ID::make(__('ID'), 'id')
        ->sortable(),

      BelongsTo::make(__('City'), 'city', City::class)
        ->sortable(),
      Text::make(__('Name'), 'name')
        ->translatable()
        ->sortable()->sortable(),
      Text::make(__('Place ID'), 'google_id')
        ->nullable()->sortable(),

    ];
  }
}