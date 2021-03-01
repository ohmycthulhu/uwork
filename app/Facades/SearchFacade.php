<?php


namespace App\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Facade to provide functionality for search helper
 *
 * @method static bool registerSearch(string $text)
 * @method static Collection getAutocomplete(string $text)
 * @method static void optimizeStorage()
 * @method static int importKeywords()
*/
class SearchFacade extends Facade
{
  protected static function getFacadeAccessor(): string
  {
    return 'search-helper';
  }
}