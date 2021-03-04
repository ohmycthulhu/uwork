<?php

namespace App\Nova\Resources;

use App\Nova\Resource;
use App\Nova\Resources\Profile\Review;
use App\Nova\Resources\User\Profile;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class User extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var string
   */
  public static $model = \App\Models\User::class;

  /**
   * The single value that should be used to represent the resource when being displayed.
   *
   * @var string
   */
  public static $title = 'email';

  public static $group = 'Common';

  /**
   * The columns that should be searched.
   *
   * @var array
   */
  public static $search = [
    'id', 'first_name', 'last_name', 'father_name', 'email', 'phone'
  ];

  /**
   * Get the fields displayed by the resource.
   *
   * @param \Illuminate\Http\Request $request
   * @return array
   */
  public function fields(Request $request): array
  {
    return [
      ID::make(__('ID'), 'id')
        ->sortable(),

      Text::make(__('First Name'), 'first_name')
        ->readonly(),

      Text::make(__('Last Name'), 'last_name')
        ->readonly(),

      Text::make(__('Father Name'), 'father_name')
        ->readonly(),

      Text::make(__('Email'), 'email')
        ->readonly(),

      Text::make(__('Phone'), 'phone')
        ->readonly(),

      HasOne::make(__('Profile'), 'profile', Profile::class),
      HasMany::make(__('Reviews'), 'reviews', Review::class),

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
