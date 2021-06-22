<?php

namespace App\Http\Controllers\API\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetCategoryByIdRequest;
use App\Models\Categories\Category;
use App\Utils\CacheAccessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
  public function index(Request $request): JsonResponse
  {
    $isDetailed = $request->input('detailed', true);
    $categories = $this->cacheAccessor->get(
      "all-$isDetailed",
      function () use ($isDetailed) {
        return $this->loadCategories($isDetailed);
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
   * @param boolean $isDetailed
   *
   * @return Collection
   */
  protected function loadCategories(bool $isDetailed = true): Collection
  {
    $query = $this->category::query()
      ->top();

    if ($isDetailed) {
      $query->with(['children']);
    }

    return $query->get();
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
  public function byId(GetCategoryByIdRequest $request, int $id): JsonResponse
  {
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
