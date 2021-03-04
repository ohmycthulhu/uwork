<?php

namespace App\Nova\Resources\Info;

use App\Nova\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Faq extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var string
   */
  public static $model = \App\Models\Info\Faq::class;

  /**
   * The single value that should be used to represent the resource when being displayed.
   *
   * @var string
   */
  public static $title = 'question';

  /**
   * The columns that should be searched.
   *
   * @var array
   */
  public static $search = [
    'id', 'question', 'answer',
  ];

  public static $group = 'Info';

  public static function indexQuery(NovaRequest $request, $query): Builder
  {
    $query->when(empty($request->get('orderBy')), function(Builder $q) {
      $q->getQuery()->orders = [];

      return $q->orderBy('order');
    });
    return parent::indexQuery($request, $query);
  }

  public static function label(): string
  {
    return 'FAQ';
  }

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

      Text::make(__('Question'), 'question')
        ->translatable()
        ->sortable(),

      Textarea::make(__('Answer'), 'answer')
        ->translatable()
        ->sortable(),

      Number::make(__('Order'), 'order')
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
