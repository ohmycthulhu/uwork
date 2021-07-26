<?php

namespace App\Nova;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource as NovaResource;

abstract class Resource extends NovaResource
{
  /**
   * Build an "index" query for the given resource.
   *
   * @param NovaRequest $request
   * @param Builder $query
   * @return Builder
   */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query;
    }

  /**
   * Build a Scout search query for the given resource.
   *
   * @param NovaRequest $request
   * @param \Laravel\Scout\Builder $query
   * @return \Laravel\Scout\Builder
   */
    public static function scoutQuery(NovaRequest $request, $query)
    {
        return $query;
    }

  /**
   * Build a "detail" query for the given resource.
   *
   * @param NovaRequest $request
   * @param Builder $query
   * @return Builder
   */
    public static function detailQuery(NovaRequest $request, $query)
    {
        return parent::detailQuery($request, $query);
    }

  /**
   * Build a "relatable" query for the given resource.
   *
   * This query determines which instances of the model may be attached to other resources.
   *
   * @param NovaRequest $request
   * @param Builder $query
   * @return Builder
   */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        return parent::relatableQuery($request, $query);
    }

    /**
     * Turn all fields readonly
     *
     * @param array $fields
     *
     * @return array
    */
    protected function makeReadonly(array $fields): array {
      return array_map(function ($f) { return $f->readonly(); }, $fields);
    }

    public static function group()
    {
      return __(parent::group());
    }
}
