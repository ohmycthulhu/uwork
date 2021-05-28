<?php

namespace App\Http\Controllers\API;

use App\Facades\MediaFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\ImageUploadRequest;
use App\Models\Media\Image;
use Exception;
use Illuminate\Http\JsonResponse;

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

    $collectionName = $request->input('collection', 'default');

    try {
      $media = MediaFacade::upload(
        $file,
        $collectionName,
      );
    } catch (Exception $e) {
      return $this->returnError(__($e->getMessage()), 505);
    }

    return $this->returnSuccess([
      'id' => $media->id,
      'media' => $media,
      'url' => $media->getFullUrl()
    ]);
  }
}
