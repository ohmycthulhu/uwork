<?php

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

    foreach ($categoriesInfo as $categoryInfo) {
      $this->createCategoryTree(
        $categoryInfo->name,
        $categoryInfo->categories ?? null
      );
    }
  }

  /**
   * Recursive method to create category
   *
   * @param string $name
   * @param ?array $subCategories
   * @param ?Category $parent
   *
   * @return Category
  */
  protected function createCategoryTree(string $name, ?array $subCategories, ?Category $parent = null): Category {
    $isHidden = !$subCategories;
    // Create category
    $category = $this->createCategory($name, $parent, $isHidden);

    // Create subcategories for category
    if ($subCategories) {
      foreach ($subCategories as $subCategory) {
        $this->createCategoryTree(
          $subCategory->name,
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
   * @return Category
  */
  protected function createCategory(string $name, ?Category $parent, bool $isHidden): Category {
    $q = $parent ? $parent->children() : Category::query();

    $category = (clone $q)->name($name)->first();

    if (!$category) {
      $category = $q->create(['name' => $name, 'is_hidden' => $isHidden]);
    }

    return $category;
  }
}
