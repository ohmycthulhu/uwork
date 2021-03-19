<?php


namespace App\Helpers;

use App\Models\Media\Image;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Helper for managing images
*/
class MediaHelper
{
  /** Image instance @var Image $image */
  protected $image;

  /** Default disk @var string */
  protected $diskDefault;

  /** Default collection @var string */
  protected $collectionDefault;

  /**
   * Creates new instance
   *
   * @param Image $image
   * @param string $diskDefault
   * @param string $collectionDefault
  */
  public function __construct(Image $image, string $diskDefault, string $collectionDefault)
  {
    $this->image = $image;
    $this->diskDefault = $diskDefault;
    $this->collectionDefault = $collectionDefault;
  }

  /**
   * Method to upload file
   *
   * @param UploadedFile $file
   * @param ?string $collectionName
   * @param ?string $class
   * @param ?int $id
   * @param ?string $disk
   *
   * @throws Exception
   *
   * @return Image
  */
  public function upload(UploadedFile $file,
                         ?string $collectionName,
                         ?string $class = null,
                         ?int $id = null,
                         ?string $disk = null): Image {
    $fileName = Str::random() . ".{$file->getClientOriginalExtension()}";

    $diskTarget = $disk ?? $this->diskDefault;
    $media = $this->image::create([
      'model_type' => $class ?? '',
      'model_id' => $id ?? 0,
      'collection_name' => $collectionName ?? $this->collectionDefault,
      'name' => $fileName,
      'file_name' => $fileName,
      'mime_type' => $file->getMimeType() ?? $file->getClientMimeType(),
      'disk' => $diskTarget,
      'size' => $file->getSize(),
      'manipulations' => '[]',
      'custom_properties' => '[]',
      'responsive_images' => '[]',
    ]);

    try {
      Storage::disk($diskTarget)
        ->makeDirectory($media->id);
      Storage::disk($diskTarget)
        ->put("{$media->id}/$fileName", File::get($file));
    } catch (Exception $e) {
      $media->forceDelete();
      throw $e;
    }

    return $media;
  }
}