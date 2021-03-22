<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Info\Faq;
use App\Models\Info\HelpCategory;
use App\Models\Info\HelpItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;

/**
 * Controller that returns general information
*/
class InfoController extends Controller
{
  /**
   * FAQ items
   *
   * @var Faq $faq
  */
  protected $faq;

  /**
   * Help category
   *
   * @var HelpCategory $helpCategory
  */
  protected $helpCategory;

  /**
   * Help item
   *
   * @var HelpItem $helpItem
  */
  protected $helpItem;

  /**
   * @param Faq $faq
   * @param HelpCategory $helpCategory
   * @param HelpItem $helpItem
  */
  public function __construct(Faq $faq, HelpCategory $helpCategory, HelpItem $helpItem)
  {
    $this->faq = $faq;
    $this->helpCategory = $helpCategory;
    $this->helpItem = $helpItem;
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
      $publicOfferPath = nova_get_setting('public_offer_path');
      $public_offer = $publicOfferPath ? URL::to("storage/$publicOfferPath") : null;

      return response()->json(compact('phone', 'apps', 'public_offer'));
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

    /**
     * Help category section
    */

    /**
     * Get all category sections
     *
     * @return JsonResponse
    */
    public function getHelpCategories(): JsonResponse {
      $categories = $this->helpCategory::query()
        ->with('topItems')
        ->withCount('items')
        ->get();

      return response()->json(['status' => 'success', 'categories' => $categories]);
    }

    /**
     * Return category by slug
     *
     * @param string $slug
     *
     * @return JsonResponse
    */
    public function getHelpCategory(string $slug): JsonResponse {
      $category = $this->helpCategory::query()
        ->slug($slug)
        ->with('items')
        ->first();

      if (!$category) {
        return response()->json(['status' => 'error', 'error' => 'Category not found'], 404);
      }

      return response()->json(['status' => 'success', 'category' => $category]);
    }

    /**
     * Return category items by slug
     *
     * @param string $slug
     *
     * @return JsonResponse
    */
    public function getHelpItem(string $slug): JsonResponse {
      $item = $this->helpItem::query()->slug($slug)->first();

      if (!$item) {
        return $this->returnError('Item not found', 404);
      }

      return response()->json(['status' => 'success', 'item' => $item]);
    }
}
