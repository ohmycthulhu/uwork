<?php

namespace Tests\Feature;

use App\Models\Info\HelpCategory;
use App\Models\Info\HelpItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class InfoTest extends TestCase
{
  use RefreshDatabase;

  /**
   * A basic feature test example.
   *
   * @return void
   */
  public function testEndpoint()
  {
    // Generate categories
    $categories = $this->generateCategories();

    // Send general request to fetch all categories
    // Check if every category is included
    $this->checkCategoriesFetching($categories->pluck('id'));


    // Send request to each category
    // Send category to each item
    foreach ($categories as $category) {
      $this->checkCategory($category);
    }

    // Remove all categories
    $this->deleteCategories();
  }

  /**
   * Check individual category
   *
   * @param HelpCategory $category
   */
  protected function checkCategory(HelpCategory $category)
  {
    $this->assertEquals($category->id, $this->get($category->slugLink)
      ->assertOk()
      ->assertJson(['category' => ['id' => $category->id]])
      ->json('category.id'));

    $items = $category->items()->get();

    foreach ($items as $item) {
      $this->assertEquals(
        $item->id,
        $this->get($item->slugLink)
          ->assertOk()
          ->assertJsonStructure(['item' => ['id', 'name', 'slug', 'order']])
          ->json('item.id')
      );
    }
  }

  /**
   * Method to check categories fetching
   *
   * @param Collection $ids
   */
  protected function checkCategoriesFetching(Collection $ids)
  {
    $response = $this->get(route('api.helpCategories.all'))
      ->assertOk()
      ->json('categories');

    $idsResponse = array_map(function ($c) {
      return $c['id'];
    }, $response);

    $this->assertEquals(sizeof($ids), sizeof($idsResponse));
    foreach ($ids as $id) {
      $this->assertTrue(in_array($id, $idsResponse));
    }
  }

  /**
   * Method to create help categories and items
   *
   * @return Collection
   */
  protected function generateCategories(): Collection
  {
    return factory(HelpCategory::class, 3)
      ->create()
      ->map(function (HelpCategory $category) {
        $category->items()
          ->createMany(
            factory(HelpItem::class, 5)
              ->make()
              ->toArray()
          );
        return $category;
      });
  }

  /**
   * Method to delete all help categories
   *
   * @return void
   */
  protected function deleteCategories()
  {
    HelpCategory::query()->forceDelete();
  }
}
