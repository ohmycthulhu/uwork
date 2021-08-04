<?php


namespace App\Helpers;

use App\Models\Media\Image;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
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
        ->put("{$media->id}/$fileName", $file->getContent());
    } catch (Exception $e) {
      $media->forceDelete();
      throw $e;
    }

    $media->order_column = $media->id;
    $media->responsive_images = $this->createResponsiveImages($media, config('images.sizes'));

    $media->save();

    return $media;
  }

  /**
   * Method to attach existing images
   *
   * @param string $class
   * @param int    $id
   * @param array  $images
   *
   * @return Collection
  */
  public function attachImages(
    string $class,
    int $id,
    array $images
  ): Collection {
    $query = $this->image::query()
      ->empty()
      ->ids($images);

    $query->update(['model_type' => $class, 'model_id' => $id]);

    return $query->get();
  }

  /**
   * Ensure existing of responsive images
   *
   * @param Image $image
   * @param array $sizes
   *
   * @return Image
  */
  public function ensureExistingResponsiveImages(Image $image, array $sizes): Image {
    $sizesExisting = json_decode($image->responsive_images, true) ?? [];

    $sizesToAdd = json_decode(json_encode($sizes));
    /* Remove all existing keys */
    foreach ($sizes as $key => $size) {
      unset($sizesToAdd[$key]);
    }

    if (!empty($sizesToAdd)) {
      $responsiveNew = $this->createResponsiveImages($image, $sizesToAdd);
      $image->responsize_images = json_encode(array_merge($sizesExisting, $responsiveNew));
      $image->save();
    }

    return $image;
  }


  /**
   * Prepare responsive images
   *
   * @param Image $image
   * @param array $sizes
   *
   * @return String
  */
  protected function createResponsiveImages(Image $image, array $sizes): string {
    $responsive = [];
    foreach ($sizes as $type => $size) {
      $res = $this->convertImage($image, $size['w'], $size['h']);
      if ($res) {
        $responsive[$type] = "{$image->id}/" . $this->generatePath($size['w'], $size['h']);
      }
    }
    return json_encode($responsive);
  }

  /**
   * Convert image
   *
   * @param Image $imageModel
   * @param int $width
   * @param int $height
   *
   * @return bool
   */
  protected function convertImage(Image $imageModel, int $width, int $height): bool {
    try {
      $path = Storage::disk($imageModel->disk)
        ->get("{$imageModel->id}/{$imageModel->file_name}");

      // Load image
      $img = \Intervention\Image\Facades\Image::make($path);
    } catch (\Exception $exception) {
      return false;
    }

    $ratio = $img->width() / $img->height();
    // Resize image
    if ($width / $height > $ratio) {
      $newH = $width / $ratio;
      $newW = $width;
    } else {
      $newH = $height;
      $newW = $height * $ratio;
    }
    $img->resize($newW, $newH);

    // Crop image
    $img->crop($width, $height);

    // Save
    $img->save(storage_path("app/public/{$imageModel->id}/".($this->generatePath($width, $height))));
    return true;
  }

  /**
   * Method for generating image path from size
   *
   * @param int $width
   * @param int $height
   *
   * @return string
  */
  protected function generatePath(int $width, int $height): string {
    return "{$width}x{$height}.jpg";
  }
}