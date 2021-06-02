<?php

namespace App\Nova\Resources;

use App\Nova\Resource;
use App\Nova\Resources\Location\City;
use App\Nova\Resources\Location\District;
use App\Nova\Resources\Location\Region;
use App\Nova\Resources\Location\Subway;
use App\Nova\Resources\Profile\Review;
use App\Nova\Resources\User\Profile;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
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
   * @param Request $request
   * @return array
   */
  public function fields(Request $request): array
  {
    return $this->makeReadonly([
      ID::make(__('ID'), 'id')
        ->sortable(),

      Image::make(__('Picture'), 'avatar'),

      Date::make(__('Birthdate'), 'birthdate'),

      Select::make(__('Sex'), 'is_male')
        ->options([
          true => __('Male'),
          false => __('Female')
        ])->displayUsingLabels(),

      Text::make(__('First Name'), 'first_name'),

      Text::make(__('Last Name'), 'last_name'),

      Text::make(__('Father Name'), 'father_name'),

      Text::make(__('Email'), 'email'),

      Text::make(__('Phone'), 'phone'),

      BelongsTo::make(__('Region'), 'region', Region::class),
      BelongsTo::make(__('City'), 'city', City::class),
      BelongsTo::make(__('District'), 'district', District::class),
      BelongsTo::make(__('Subway'), 'subway', Subway::class),

      HasOne::make(__('Profile'), 'profile', Profile::class),
      HasMany::make(__('Reviews'), 'reviews', Review::class),
    ]);
  }

  /**
   * Get the cards available for the request.
   *
   * @param Request $request
   * @return array
   */
  public function cards(Request $request)
  {
    return [];
  }

  /**
   * Get the filters available for the resource.
   *
   * @param Request $request
   * @return array
   */
  public function filters(Request $request)
  {
    return [];
  }

  /**
   * Get the lenses available for the resource.
   *
   * @param Request $request
   * @return array
   */
  public function lenses(Request $request)
  {
    return [];
  }

  /**
   * Get the actions available for the resource.
   *
   * @param Request $request
   * @return array
   */
  public function actions(Request $request)
  {
    return [];
  }
}
