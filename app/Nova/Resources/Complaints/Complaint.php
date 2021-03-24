<?php

namespace App\Nova\Resources\Complaints;

use App\Models\Profile\Review;
use App\Models\User\Profile;
use App\Nova\Actions\Complaints\CloseComplaint;
use App\Nova\Filters\Complaints\StateFilter;
use App\Nova\Filters\RelationFilter;
use App\Nova\Resource;
use App\Nova\Resources\User;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class Complaint extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var string
   */
  public static $model = \App\Models\Complaints\Complaint::class;

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
    'id', 'text', 'reason_other'
  ];

  public static $group = 'Complaints';

  /**
   * Get the fields displayed by the resource.
   *
   * @param \Illuminate\Http\Request $request
   * @return array
   */
  public function fields(Request $request)
  {
    return $this->makeReadonly([
      ID::make(__('ID'), 'id')->sortable(),

      BelongsTo::make(__('Reason'), 'type')->sortable(),

      BelongsTo::make(__('User'), 'user', User::class),

      Text::make(__('Other reason'), 'reason_other')->sortable(),

      Textarea::make(__('Text'), 'text'),

      MorphTo::make(__('Object'), 'complaintable')
        ->types([
          Profile::class => __('Profile'),
          Review::class => __('Review'),
        ])
    ]);
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
    return [
      StateFilter::make(),
      RelationFilter::make('type_id', \App\Models\Complaints\ComplaintType::class),
    ];
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
    return [
      CloseComplaint::make()
    ];
  }
}
