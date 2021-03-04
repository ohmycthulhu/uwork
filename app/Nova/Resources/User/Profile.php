<?php

namespace App\Nova\Resources\User;

use App\Nova\Resource;
use App\Nova\Resources\Profile\Review;
use App\Nova\Resources\User;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Profile extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User\Profile::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'about', 'phone'
    ];

    public static $group = 'Profiles';

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

          Textarea::make(__('About'), 'about')->readonly(),

          Text::make(__('Phone'), 'phone')->readonly(),

          Image::make(__('Picture'), 'picture')->readonly(),

          Number::make(__('Views count'), 'views_count')
            ->readonly(),
          Number::make(__('Open count'), 'open_count')
            ->readonly(),
          Number::make(__('Reviews count'), 'reviews_count')
            ->readonly(),
          Number::make(__('Rating (total)'), 'rating')
            ->readonly(),
          Number::make(__('Rating (quality)'), 'rating_quality')
            ->readonly(),
          Number::make(__('Rating (time)'), 'rating_time')
            ->readonly(),
          Number::make(__('Rating (price)'), 'rating_price')
            ->readonly(),

          BelongsTo::make(__('User'), 'user', User::class),
          HasMany::make(__('Specialities'), 'specialities', ProfileSpeciality::class),
          HasMany::make(__('Reviews'), 'reviews', Review::class),
          MorphMany::make(__('Images'), 'media', \App\Nova\Resources\Media\Image::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
