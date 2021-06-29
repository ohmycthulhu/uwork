<?php


namespace App\Modifiers;


use App\Models\Categories\Category;
use App\Models\User\Profile;
use App\Search\Builders\ProfileSearchBuilder;
use Illuminate\Support\Collection;

class CategoriesModifier extends Modifier
{
  /**
   * Method for adding breadcrumbs
   *
   * @return $this
  */
  public function addBreadcrumbs(): self {
    return $this->addStep('breadcrumb');
  }

  /**
   * Method for adding the services
   *
   * @param ?Profile $profile
   * @param ?int $parentId = null,
   * @param bool $addTotal = false,
   * @param bool $addServicesList = false
   *
   * @return $this
  */
  public function addServices(?Profile $profile, ?int $parentId, bool $addTotal, bool $addServicesList): self {
    $this->addStep('services', compact('profile', 'parentId', 'addTotal', 'addServicesList'));
    return $this;
  }

  /**
   * Method for adding the profiles count
   *
   * @return $this
  */
  public function addProfilesCount(): self {
    $this->addStep('profilesCount');
    return $this;
  }

  /**
   * Function to execute
  */
  protected function apply(Collection $data, string $name, array $params): Collection
  {
    if ($name === 'breadcrumb') {
      return $this->appendBreadcrumbs($data);
    }
    if ($name === 'services') {
      return $this->appendServices($data, $params);
    }
    if ($name === 'profilesCount') {
      return $this->appendProfilesCount($data);
    }
    return $data;
  }


  /**
   * Function to append the breadcrumbs
   *
   * @param Collection $categories
   *
   * @return Collection
  */
  protected function appendBreadcrumbs(Collection $categories): Collection {
    $parentCategoriesIds = $categories->reduce(function (Collection $acc, Category $category) {
      return $acc->merge($category->getCategoryPathIdsAttribute())->unique();
    }, new Collection());
    $parentCategories = Category::query()
      ->whereIn('id', $parentCategoriesIds)
      ->get();

    return $categories->map(function (Category $category) use ($parentCategories) {
      $breadcrumb = array_map(
        function (int $id) use ($parentCategories) {
          return $parentCategories->find($id);
        },
        $category->getCategoryPathIdsAttribute()
      );
      return array_merge($category->toArray(), compact('breadcrumb'));
    });
  }

  /**
   * Method for adding profiles count
   *
   * @param Collection $categories
   *
   * @return Collection
  */
  protected function appendProfilesCount(Collection $categories): Collection {
    return $categories->map(function ($category) {
      return array_merge($category, [
        'profiles_count' => $this->getProfilesCount($category['id'])
      ]);
    });
  }

  protected function getProfilesCount(int $categoryId): int {
    return (new ProfileSearchBuilder(new Profile))->setCategories([], $categoryId)
      ->execute()
      ->getTotal();
  }

  /**
   * Method for adding the services
   *
   * @param Collection $categories
   * @param array $params
   *
   * @return Collection
  */
  protected function appendServices(Collection $categories, array $params): Collection {
    $profile = $params['profile'];
    $parentId = $params['parentId'];
    $addTotal = $params['addTotal'];
    $addServicesList = $params['addServicesList'];

    $countCallback = function () {
      return 0;
    };
    $isSelectedCallback = function () {
      return false;
    };
    if ($profile) {
      /* @var Collection $specialities */
      $specialities = $profile->specialities()
        ->category($parentId)
        ->pluck('category_path', 'category_id');
      if (!empty($specialities)) {
        $countCallback = function (Category $category) use ($specialities) {
          return $specialities->filter(function (string $path) use ($category) {
            return str_contains($path, " {$category->id} ");
          })->count();
        };
        $isSelectedCallback = function (Category $category, int $count) use ($specialities) {
          return $count > 0 || $specialities->has($category->id);
        };
      }
    }

    return $categories->map(function (Category $category) use ($addTotal, $addServicesList, $countCallback, $isSelectedCallback) {
      $total = $addTotal ? $category->getServicesCountAttribute() : null;
      $services = $addServicesList ? $category->getServicesAttribute() : null;
      $count = $countCallback($category);
      return array_merge(
        ['category' => $category],
        $total !== null ? compact('total') : [],
        $services !== null ? compact('services') : [],
        [
          'count' => $count,
          'selected' => $isSelectedCallback($category, $count),
        ]
      );
    });
  }
}