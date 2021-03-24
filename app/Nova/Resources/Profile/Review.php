<?php

namespace App\Nova\Resources\Profile;

use App\Nova\Resource;
use App\Nova\Resources\Complaints\Complaint;
use App\Nova\Resources\User;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class Review extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var string
   */
  public static $model = \App\Models\Profile\Review::class;

  /**
   * The single value that should be used to represent the resource when being displayed.
   *
   * @var string
   */
  public static $title = 'headline';

  /**
   * The columns that should be searched.
   *
   * @var array
   */
  public static $search = [];

  public static $group = 'Profiles';

  /**
   * Get the fields displayed by the resource.
   *
   * @param Request $request
   * @return array
   */
  public function fields(Request $request)
  {
    return [
      ID::make(__('ID'), 'id')->sortable(),

      Text::make(__('Headline'), 'headline')->readonly()->sortable(),
      Textarea::make(__('Text'), 'Text')->readonly(),

      BelongsTo::make(__('User'), 'user', User::class)
        ->readonly()->sortable(),
      BelongsTo::make(__('Profile'), 'profile', User\Profile::class)
        ->readonly()->sortable(),
      BelongsTo::make(__('Speciality'), 'speciality', User\ProfileSpeciality::class)
        ->readonly()->sortable(),

      Text::make('IP', 'ip_addr')->readonly()->onlyOnDetail(),

      Number::make(__('Rating (quality)'), 'rating_quality')
        ->readonly()->onlyOnDetail(),
      Number::make(__('Rating (time)'), 'rating_time')
        ->readonly()->onlyOnDetail(),
      Number::make(__('Rating (price)'), 'rating_price')
        ->readonly()->onlyOnDetail(),

      MorphMany::make(__('Complaints'), 'complaints', Complaint::class),
    ];
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
