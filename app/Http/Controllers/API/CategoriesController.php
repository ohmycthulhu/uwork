<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categories\Category;
use App\Utils\CacheAccessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

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
  /* @var CacheAccessor $cacheAccessor */
  protected $cacheAccessor;

  /**
   * Creates new instance of controller
   *
   * @param Category $category
   */
  public function __construct(Category $category)
  {
    $this->category = $category;
    $this->cacheAccessor = new CacheAccessor("categories-");
  }

  /**
   * Returns list of all categories
   *
   * @return JsonResponse
   */
  public function index(): JsonResponse
  {
    $categories = $this->cacheAccessor->get(
      'all',
      function () {
        return $this->loadCategories();
      },
      true
    );

    return $this->returnSuccess([
      'categories' => $categories
    ]);
  }

  /**
   * Get list of categories
   *
   * @return Collection
   */
  protected function loadCategories(): Collection
  {
    return $this->category::query()
      ->with(['children.children'])
      ->top()
      ->get();
  }

  /**
   * Returns information about specific category by slug
   *
   * @param string $slug
   *
   * @return JsonResponse
   */
  public function bySlug(string $slug): JsonResponse
  {
    $category = $this->category::query()
      ->slug($slug)
      ->with(['parent', 'children.children.children'])
      ->first();

    if (!$category) {
      return $this->returnError(__('Category not found'), 404);
    }

    return $this->returnSuccess(['category' => $category]);
  }
}
