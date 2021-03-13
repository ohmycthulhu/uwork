<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Info\Faq;
use Illuminate\Http\JsonResponse;

/**
 * Controller that returns general information
*/
class InfoController extends Controller
{
  /**
   * FAQ items
   *
   * @var Faq
  */
  protected $faq;

  /**
   * @param Faq $faq
  */
  public function __construct(Faq $faq)
  {
    $this->faq = $faq;
  }

  /**
     * Returns all general information
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse {
      $phone = nova_get_setting('phone');
      $apps = [
        'android' => nova_get_setting('app_android'),
        'ios' => nova_get_setting('app_ios'),
      ];

      return response()->json(compact('phone', 'apps'));
    }

    /**
     * Returns about us page content
     *
     * @return JsonResponse
    */
    public function about(): JsonResponse {
      $aboutUs = json_decode(nova_get_setting('about_us'), true) ?? ['en' => ''];

      return response()->json([
        'about_us' => $aboutUs
      ]);
    }

    /**
     * Returns faq items
     *
     * @return JsonResponse
    */
    public function faq(): JsonResponse {
      $faq = $this->faq::all();

      return response()->json([
        'faq' => $faq
      ]);
    }
}
