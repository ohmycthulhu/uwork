<?php

namespace App\Console\Commands;

use App\Facades\MediaFacade;
use App\Models\Media\Image;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnsureResponsiveImages extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'images:repair';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Repairs responsive images';

  /* Array of available sizes */
  protected $sizes;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->sizes = config('images.sizes');
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    echo "Started to fixing images\n";
    /***
     * For each image ensure that all sizes exists
     *
     */
    $query = Image::query();

    $totalAmount = $query->count();

    echo "{$query->count()} images in system\n";

    Image::query()->batchExecute(function (Image $image, $index) use ($totalAmount) {
      echo "Processing image #{$image->id} | $index / $totalAmount\n";
      try {
        return MediaFacade::ensureExistingResponsiveImages($image, $this->sizes);
      } catch (\Exception $exception) {
        echo "Failed to process image. {$exception->getMessage()}\n";
        Log::error($exception->getMessage());
        return null;
      }
    }, 15);

    echo "Ended processing files\n";

    return 0;
  }
}
