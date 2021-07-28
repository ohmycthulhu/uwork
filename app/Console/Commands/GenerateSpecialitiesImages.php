<?php

namespace App\Console\Commands;

use App\Facades\MediaFacade;
use App\Models\Media\Image;
use App\Models\User\ProfileSpeciality;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class GenerateSpecialitiesImages extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'specialities:generate';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generates the image for specialities';

  protected $imgDirectory;
  protected $tempFile;
  protected $imageCountMin, $imageCountMax;
  protected $batchSize;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->imgDirectory = storage_path('images/specialities');
    $this->tempFile = storage_path('images/image_dest.jpg');
    $this->imageCountMin = 1;
    $this->imageCountMax = 5;
    $this->batchSize = 15;
  }

  /**
   * Get paths for images
   *
   * @return array
   */
  protected function getImagePaths(): array
  {
    $images = File::allFiles($this->imgDirectory);
    return array_map(function (SplFileInfo $fi) {
      return $fi->getPathname();
    }, $images);
  }

  /**
   * Execute the command for speciality
   *
   * @param ProfileSpeciality $speciality
   * @param array $images
   *
   * @return ProfileSpeciality
   */
  protected function handleSpeciality(ProfileSpeciality $speciality, array $images): ProfileSpeciality
  {
    echo "Processing speciality #{$speciality->id}\n";

    shuffle($images);

    return $this->updateImages(
      $speciality,
      array_slice($images, 0, rand($this->imageCountMin, $this->imageCountMax))
    );
  }

  /**
   * Update images for speciality
   *
   * @param ProfileSpeciality $speciality
   * @param array $images
   *
   * @return ProfileSpeciality
  */
  protected function updateImages(ProfileSpeciality $speciality, array $images): ProfileSpeciality {
    $speciality->media()->forceDelete();

    foreach ($images as $image) {
      $this->uploadImage($speciality, $image);
    }

    return $speciality;
  }

  /**
   *  Upload an image for speciality
   *
   * @param ProfileSpeciality $speciality
   * @param string $image
   *
   * @return Image
  */
  protected function uploadImage(ProfileSpeciality $speciality, string $image): Image {
    File::copy($image, $this->tempFile);

    $image = new UploadedFile(
      $this->tempFile,
      "example.jpg",
      "images/jpeg",
      null
    );

    return MediaFacade::upload(
      $image,
      'default',
      ProfileSpeciality::class,
      $speciality->id,
    );
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $images = $this->getImagePaths();

    $count = ProfileSpeciality::query()
      ->batchExecute(function (ProfileSpeciality $speciality) use ($images) {
        return $this->handleSpeciality($speciality, $images);
      }, $this->batchSize);

    echo "Processed $count specialities\n";

    return 0;
  }
}
