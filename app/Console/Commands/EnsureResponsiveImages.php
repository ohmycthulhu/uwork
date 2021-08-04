<?php

namespace App\Console\Commands;

use App\Facades\MediaFacade;
use App\Models\Media\Image;
use Illuminate\Console\Command;

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
    /***
     * For each image ensure that all sizes exists
     *
     */
    Image::query()->batchExecute(function (Image $image) {
      return MediaFacade::ensureExistingResponsiveImages($image, $this->sizes);
    }, 15);
    return 0;
  }
}
