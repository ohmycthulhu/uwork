<?php


namespace App\Facades;


use App\Models\Media\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for managing image files
 *
 * @method static Image upload(UploadedFile $file, ?string $collectionName, ?string $class = null, ?int $id = null, ?string $disk = null)
 *
*/
class MediaFacade extends Facade
{
  /**
   * Facade accessor
   *
   * @return string
  */
  protected static function getFacadeAccessor(): string
  {
    return "media-facade";
  }
}