<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User\Profile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GenerateSitemapController extends Controller
{
  /* @var Profile */
  protected $profile;

  /**
   * Creates new instance of controller
   *
   * @param Profile $profile
  */
  public function __construct(Profile $profile)
  {
    $this->profile = $profile;
  }

  /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return View
     */
    public function __invoke(Request $request): View
    {
      // Get all confirmed profiles
      $profiles = $this->profile::query()->public()->pluck('id');

      $frontUrl = config('app.front.base_url');

      $date = date('Y-m-d');

      // Pass profiles to sitemap xml
      return view('sitemap', compact('profiles', 'frontUrl', 'date'));
    }
}
