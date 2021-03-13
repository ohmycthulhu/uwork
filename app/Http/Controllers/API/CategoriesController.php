<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categories\Category;
use Illuminate\Http\JsonResponse;

/**
 * Controller for working with categories
 * Supports only getting categories info
*/
class CategoriesController extends Controller
{
  /**
   * @var Category $category
  */
    protected $category;

    /**
     * Creates new instance of controller
     *
     * @param Category $category
    */
    public function __construct(Category $category)
    {
      $this->category = $category;
    }

    /**
     * Returns list of all categories
     *
     * @return JsonResponse
    */
    public function index(): JsonResponse {
      $categories = $this->category::query()
        ->with(['children.children.children.children'])
        ->top()
        ->get();

      return response()->json([
        'categories' => $categories
      ]);
    }

    /**
     * Returns information about specific category by slug
     *
     * @param string $slug
     *
     * @return JsonResponse
    */
    public function bySlug(string $slug): JsonResponse {
      $category = $this->category::query()
        ->slug($slug)
        ->with(['parent', 'children.children.children'])
        ->first();

      if (!$category) {
        return response()->json(['error' => 'Category not found'], 404);
      }

      return response()->json(['category' => $category]);
    }
}
