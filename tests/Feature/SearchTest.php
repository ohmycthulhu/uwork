<?php

namespace Tests\Feature;

use App\Models\Categories\Category;
use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\Region;
use App\Models\User\Profile;
use App\Models\User\ProfileSpeciality;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class SearchTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Method to test search
   *
   * @return void
   */
  public function testSearch()
  {
    // Create profiles, regions, cities and districts
    $this->fillDatabase(true);

    $response = $this->get(route('api.profiles.search'))
      ->assertOk()
      ->assertJsonStructure([
        'result' => [
          'data',
          'next_page_url',
          'total',
          'current_page',
        ]
      ]);

    // Check the structure of request
    $profiles = $response->json('result.data');
    foreach ($profiles as $profile) {
      $this->assertNotNull($profile['speciality'] ?? null);
    }

    // Load profiles by region
    $region = Profile::query()->inRandomOrder()->first()->region_id;

    $response = $this->get(route('api.profiles.search', ['region_id' => $region]))
      ->assertOk()
      ->json('result.data');

    $this->assertIsArray($response);
    $this->assertNotEmpty($response);
    foreach ($response as $p) {
      $this->assertEquals($region, $p['region_id']);
    }

    // Search profiles by cities
    $city = Profile::query()->inRandomOrder()->first()->city_id;
    $profiles = $this->get(route('api.profiles.search', ['city_id' => $city]))
      ->assertOk()
      ->json('result.data');

    $this->assertNotEmpty($profiles);
    foreach ($profiles as $profile) {
      $this->assertEquals($city, $profile['city_id']);
    }

    // Search profiles by districts
    $district = Profile::query()->whereNotNull('district_id')->inRandomOrder()->first()->district_id;
    $profiles = $this->get(route('api.profiles.search', ['district_id' => $district]))
      ->assertOk()
      ->json('result.data');

    $this->assertNotEmpty($profiles);
    foreach ($profiles as $profile) {
      $this->assertEquals($district, $profile['district_id']);
    }

    // Search profile by categories
    $category = Category::query()
      ->inRandomOrder()
      ->first();

    $cats = [$category->id];

    $profiles = $this->get(route('api.profiles.search', ['categories' => $cats]))
      ->assertOk()
      ->json('result.data');

    foreach ($profiles as $profile) {
      // Get all category ids from all specialities
      $catIds = array_reduce(
        $profile['specialities'],
        function ($acc, $s) {
          $categories = array_map('trim', explode(' ', $s['category_path']));
          return array_merge($acc, $categories);
        },
        []
      );

      // Collect all names of categories
      $names = Category::query()
        ->whereIn('id', $catIds)
        ->pluck('name')
        ->join(',');

      // Check if keyword is presented in categories' names
//      $this->assertStringContainsStringIgnoringCase($keyword, $names);
    }

    // Search profile by category
    $category = Category::query()
      ->top()
      ->inRandomOrder()
      ->first();

    $profiles = $this->get(route('api.profiles.search', ['category_id' => $category->id]))
      ->assertOk()
      ->json('result.data');

    foreach ($profiles as $profile) {
      $spec = array_map(function ($s) {return $s['category_path'];}, $profile['specialities']);
      $this->assertStringContainsString($category->id, join(',',$spec));
    }

    // Test price range search
    $priceMin = ProfileSpeciality::query()->first()->price;
    $priceMax = $priceMin;

    $profiles = $this->get(
      route('api.profiles.search', ['sort' => 'price', 'dir' => 'desc', 'price_min' => $priceMin, 'price_max' => $priceMax])
    )
      ->assertOk()
      ->json('result.data');


    $r = array_filter($profiles, function ($profile) use ($priceMin, $priceMax) {
      return $profile['speciality'] && (
      $profile['speciality']['price'] < $priceMin || $profile['speciality']['price'] > $priceMax);
    });

    $this->assertEmpty($r);

    $this->clearDatabase();
  }

  public function testRandom () {
    // Fill database
    $this->fillDatabase(true);

    // Send empty request to random
    $this->get(route('api.profiles.random'))
      ->assertOk();

    // Send request with non-existing category id
    $categoryId = rand(10000, 20000);
    $this->get(route('api.profiles.random', ['category_id' => $categoryId]))
      ->assertStatus(404)
      ->assertJsonStructure(['status', 'error']);

    // Send request with every category
    $categories = Category::query()->get();
    foreach ($categories as $category) {
      $this->get(route('api.profiles.random', ['category_id' => $category->id]))
        ->assertOk()
        ->assertJsonStructure(['profiles', 'category']);
    }

    // Clear database
    $this->clearDatabase();
  }
}
