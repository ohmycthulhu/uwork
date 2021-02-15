<?php

namespace Tests\Feature;

use App\Models\Categories\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Method to test creating and deleting of categories
   *
   * @return void
  */
  public function testDatabase() {
    // Create categories
    $categories = factory(Category::class, 5)
      ->create();

    $this->assertTrue(true);

    foreach ($categories as $category) {
      $category->children()->createMany(
        factory(Category::class, 5)
        ->make()
        ->toArray()
      );
    }

    // Check amount of created categories
    $this->assertEquals(5, Category::query()->top()->count());
    $this->assertEquals(5 * 5, Category::query()->child()->count());

    // Try to delete category
    // Delete all categories
    foreach ($categories as $category) {
      $category->forceDelete();
    }

    $this->assertPostConditions();
  }

  /**
   * Method to test accessing categories
   *
   * @return void
  */
  public function testAPI() {
    // Create categories
    $this->createCategories();

    // Fetch categories list from API
    $fetchedCategories = $this->get(route('api.categories.all'))
      ->assertOk()
      ->json('categories');

    // Check if all parent categories are top level
    foreach ($fetchedCategories as $category) {
      $this->assertNull($category['parent_id']);
    }

    $categories = Category::all();
    // Fetch category of specific parent
    // Check if all categories belongs to the parent
    foreach ($categories as $category) {
      $response = $this->get($category->slugLink)
        ->assertOk();

      foreach ($response->json('category.children') as $child) {
        $this->assertEquals($category->id, $child['parent_id']);
      }
    }

    // Delete categories
    $this->deleteCategories();
  }

  /**
   * Method to create categories
   *
   * @return Collection
  */
  public function createCategories(): Collection {
    $categories = factory(Category::class, 5)->create();
    foreach ($categories as $category) {
      $category->children()
        ->createMany(
          factory(Category::class, 5)
            ->make()
            ->toArray()
        );
    }
    return $categories;
  }

  /**
   * Method to delete all categories
   *
   * @return void
  */
  public function deleteCategories() {
    Category::query()->forceDelete();
  }
}
