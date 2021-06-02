<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetCategoryByIdRequest;
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
      ->with(['children'])
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

  /**
   * Returns category information by id
   * User can regulate the nesting level
   *
   * @param GetCategoryByIdRequest $request
   * @param int $id
   *
   * @return JsonResponse
  */
  public function byId(GetCategoryByIdRequest $request, int $id): JsonResponse {
    $query = $this->category::query()
      ->id($id);

    $children = join(
      '.',
      array_fill(0, $request->input('level', 2), 'children')
    );

    $query->with(['parent', $children]);

    $category = $query->first();

    if ($category) {
      return $this->returnSuccess(compact('category'));
    } else {
      return $this->returnError(__('Category not found'), 404);
    }
  }
}
