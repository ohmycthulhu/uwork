<?php

namespace App\Nova\Resources\User;

use App\Nova\Resource;
use App\Nova\Resources\Categories\Category;
use App\Nova\Resources\Media\Image;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class ProfileSpeciality extends Resource
{
  /**
   * The model the resource corresponds to.
   *
   * @var string
   */
  public static $model = \App\Models\User\ProfileSpeciality::class;

  /**
   * The single value that should be used to represent the resource when being displayed.
   *
   * @var string
   */
  public static $title = 'name';

  /**
   * The columns that should be searched.
   *
   * @var array
   */
  public static $search = [
    'id', 'category_id', 'name'
  ];

  public static $group = 'Categories';

  /**
   * Get the fields displayed by the resource.
   *
   * @param Request $request
   * @return array
   */
  public function fields(Request $request): array
  {
    return [
      ID::make(__('ID'), 'id')->sortable(),

      Text::make(__('Name'), 'name')->readonly()->sortable(),

      Number::make(__('Price'), 'price')->readonly()->sortable(),

      BelongsTo::make(__('Category'), 'category', Category::class)
        ->sortable(),

      BelongsTo::make(__('Profile'), 'profile', Profile::class)
        ->sortable(),

      MorphMany::make(__('Media'), 'media', Image::class),
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
