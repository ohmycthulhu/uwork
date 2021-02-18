<?php

namespace Tests\Feature;

use App\Models\Media\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Models\Media;
use Tests\TestCase;

class FileTest extends TestCase
{
  use RefreshDatabase;
  /**
   * Method to test file upload
   *
   * @return void
  */
  public function testUpload() {
    // Prepare form
    $path = storage_path('test/image.jpg');
    $fileName = Str::random().'.jpg';
    $this->assertFileExists($path);
    $file = new UploadedFile($path, $fileName, filesize($path), null, true);

    $form = [
      'image' => $file,
    ];

    // Send image to API
    $this->post(route('api.files'))
      ->assertStatus(403);
    $fileModel = $this->post(route('api.files'), $form)
      ->assertOk()
      ->json('media');

    // Check if image exists in database and in storage
    $this->assertEquals(1, Media::query()->count());
    $this->assertEquals(1, Image::query()->empty()->count());
    $this->assertFileExists(storage_path("app/public/{$fileModel['id']}/{$fileModel['file_name']}"));


    // Delete image
    Media::query()
      ->forceDelete();
  }
}
