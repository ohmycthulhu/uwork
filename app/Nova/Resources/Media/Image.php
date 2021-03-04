<?php

namespace App\Nova\Resources\Media;

use App\Nova\Resource;
use App\Nova\Resources\User\Profile;
use App\Nova\Resources\User\ProfileSpeciality;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;

class Image extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var string
   */
  public static $model = \App\Models\Media\Image::class;

  /**
   * The single value that should be used to represent the resource when being displayed.
   *
   * @var string
   */
  public static $title = 'id';

  public static $displayInNavigation = false;

  /**
   * Get the fields displayed by the resource.
   *
   * @param \Illuminate\Http\Request $request
   * @return array
   */
  public function fields(Request $request): array
  {
    return [
      ID::make(__('ID'), 'id')->sortable(),

      \Laravel\Nova\Fields\Image::make('Path', 'url')
        ->hideWhenUpdating()
        ->hideWhenCreating(),

      MorphTo::make(__('Object'), 'model')
        ->types([
          Profile::class
        ]),

      MorphTo::make(__('Associated with'), 'modelAdditional')
        ->types([
          ProfileSpeciality::class,
        ])->nullable()
    ];
  }

  /**
   * Get the cards available for the request.
   *
   * @param \Illuminate\Http\Request $request
   * @return array
   */
  public function cards(Request $request)
  {
    return [];
  }

  /**
   * Get the filters available for the resource.
   *
   * @param \Illuminate\Http\Request $request
   * @return array
   */
  public function filters(Request $request)
  {
    return [];
  }

  /**
   * Get the lenses available for the resource.
   *
   * @param \Illuminate\Http\Request $request
   * @return array
   */
  public function lenses(Request $request)
  {
    return [];
  }

  /**
   * Get the actions available for the resource.
   *
   * @param \Illuminate\Http\Request $request
   * @return array
   */
  public function actions(Request $request)
  {
    return [];
  }
}
