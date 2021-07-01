]<?php

use App\Models\Categories\Category;
use Illuminate\Database\Seeder;

class RealCategoriesSeeder extends Seeder
{
  /**
   * Path to file to read from
   *
   * @var string
   */
  protected $filePath;

  /**
   * Create new instance of class
   *
   * @param ?string $path
   */
  public function __construct(?string $path = null)
  {
    $this->filePath = $path ?? storage_path('data/categories.json');
  }

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $categoriesInfo = json_decode(\Illuminate\Support\Facades\File::get($this->filePath));

    $invalidIcons = array_reduce($categoriesInfo, function ($acc, $c) {
      return array_merge($acc, $this->getInvalidIcons($c));
    }, []);

    if ($invalidIcons) {
      throw new Exception("These icons do not exists: " . join(', ', $invalidIcons));
    }

    $this->copyIcons($categoriesInfo);

    foreach ($categoriesInfo as $index => $categoryInfo) {
      echo "Importing categories | " . ($index + 1) . " / " . (sizeof($categoriesInfo)) . "\n";
      $this->createCategoryTree(
        $categoryInfo,
        $categoryInfo->categories ?? null
      );
    }
  }

  /**
   * Get invalid icon paths
   *
   * @param object $category
   *
   * @return array
   */
  protected function getInvalidIcons(object $category): array
  {
    $result = [];

    if (!$this->checkIconExists($category->icon_default ?? null)) {
      $result[] = $category->icon_default;
    }

    if (!$this->checkIconExists($category->icon_selected ?? null)) {
      $result[] = $category->icon_selected;
    }

    return $result;
  }

  /**
   * Checks if icon exists
   *
   * @param ?string $path
   *
   * @return bool
   */
  protected function checkIconExists(?string $path): bool
  {
    return !$path || file_exists(storage_path("data/icons/$path"));
  }

  /**
   * Populate the icons
   *
   * @param array $categories
   */
  protected function copyIcons(array $categories) {
      \Illuminate\Support\Facades\File::ensureDirectoryExists(
          public_path("storage/categories")
      );

      foreach ($categories as $category) {
          if ($icon = ($category->icon_default ?? null)) {
              $this->copyIcon($icon);
          }
          if ($icon = ($category->icon_selected ?? null)) {
              $this->copyIcon($icon);
          }
      }
  }

  /**
   * Copy the icon
   *
   * @param string $path
   */
  protected function copyIcon(string $path)
  {
    \Illuminate\Support\Facades\File::copy(
      storage_path("data/icons/$path"),
      public_path("storage/$path")
    );
  }

  /**
   * Recursive method to create category
   *
   * @param object $info
   * @param ?array $subCategories
   * @param ?Category $parent
   *
   * @return ?Category
   */
  protected function createCategoryTree(object $info, ?array $subCategories, ?Category $parent = null): ?Category
  {
    $isHidden = false && !$subCategories;
    // Create category
    $category = $this->createCategory($info->name, $parent, $isHidden);

    // Create subcategories for category
    if ($category && $subCategories) {
      $category->update([
        'icon_default' => $info->icon_default ?? null,
        'icon_selected' => $info->icon_selected ?? null,
      ]);

      foreach ($subCategories as $subCategory) {
        $this->createCategoryTree(
          $subCategory,
          $subCategory->categories ?? null,
          $category
        );
      }
    }

    // Return category
    return $category;
  }

  /**
   * Method to create category
   *
   * @param string $name
   * @param ?Category $parent
   * @param bool $isHidden
   *
   * @return ?Category
   */
  protected function createCategory(string $name, ?Category $parent, bool $isHidden): ?Category
  {
    if ($parent && $parent->name == $name) {
      return null;
    }
    $q = $parent ? $parent->children() : Category::query();

    $category = (clone $q)->name($name)->first();

    if (!$category) {
      echo "$name not found\n";
      $category = $q->create(['name' => $name, 'is_hidden' => $isHidden]);
    }

    return $category;
  }
}
