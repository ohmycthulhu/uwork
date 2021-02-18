<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageUploadRequest;
use App\Models\Media\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Models\Media;

class FileController extends Controller
{
  protected $disk;
  protected $media;

  /**
   * Creates new instance of controller
   *
   * @param Image $media
   * @param string $disk
   */
  public function __construct(Image $media, string $disk = 'public')
  {
    $this->media = $media;
    $this->disk = $disk;
  }

  /**
   * Method to upload image to server
   *
   * @param ImageUploadRequest $request
   *
   * @return JsonResponse
   */
  public function uploadImage(ImageUploadRequest $request): JsonResponse
  {
    $file = $request->file('image');

    $fileName = Str::random() . ".{$file->getClientOriginalExtension()}";

    $collectionName = $request->input('collection', 'default');

    $media = Media::create([
      'model_type' => '',
      'model_id' => 0,
      'collection_name' => $collectionName,
      'name' => $fileName,
      'file_name' => $fileName,
      'mime_type' => $file->getClientMimeType(),
      'disk' => $this->disk,
      'size' => $file->getSize(),
      'manipulations' => '[]',
      'custom_properties' => '[]',
      'responsive_images' => '[]'
    ]);

    try {
      Storage::disk($this->disk)
        ->makeDirectory($media->id);
      Storage::disk($this->disk)
        ->put("{$media->id}/$fileName", File::get($file));
    } catch (\Exception $e) {
      $media->delete();
      return response()->json(['error' => $e->getMessage()], 505);
    }

    return response()->json([
      'status' => 'success',
      'id' => $media->id,
      'media' => $media,
      'url' => $media->getFullUrl()
    ]);
  }
}
