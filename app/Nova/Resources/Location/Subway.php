<?php

namespace App\Nova\Resources\Location;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Subway extends LocationResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Location\Subway::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

          Text::make(__('Name'), 'name')->sortable(),

          Text::make(__('Line'), 'line')->sortable(),
          Text::make(__('Color'), 'color')->sortable(),

          BelongsTo::make(__('City'), 'city', City::class)->sortable(),
          BelongsTo::make(__('District'), 'district', District::class)->sortable(),
        ];
    }
}
