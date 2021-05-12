<?php

namespace App\Nova\Resources\Info;

use App\Nova\Resource;
use Froala\NovaFroalaField\Froala;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class TextStatistic extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var string
   */
  public static $model = \App\Models\Info\TextStatistic::class;

  /**
   * The single value that should be used to represent the resource when being displayed.
   *
   * @var string
   */
  public static $title = 'key';

  /**
   * The columns that should be searched.
   *
   * @var array
   */
  public static $search = [
    'key',
  ];

  public static $group = 'Info';

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
      Text::make(__('Name'), 'name')->sortable(),
      Select::make(__('Key'), 'key')
        ->sortable()
        ->options(array_combine(config('info.texts'), config('info.texts'))),
      Froala::make(__('Text'), 'text'),
      Number::make(__('Ups'), 'upvotes')
        ->exceptOnForms()
        ->sortable(),
      Number::make(__('Downs'), 'downvotes')
        ->exceptOnForms()
        ->sortable(),
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
