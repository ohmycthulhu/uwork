<?php

namespace Tests\Feature;

use App\Models\Categories\Category;
use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\Region;
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
    $this->fillDatabase();

    $this->get(route('api.profiles.search'))
      ->assertOk()
      ->assertJsonStructure([
        'result' => [
          'data',
          'next_page_url',
          'total',
          'current_page',
        ]
      ]);


    // Load profiles by region
    $region = Region::query()->inRandomOrder()->first();

    $response = $this->get(route('api.profiles.search', ['region_id' => $region->id]))
      ->assertOk()
      ->json('result.data');

    $this->assertIsArray($response);
    foreach ($response as $p) {
      $this->assertEquals($region->id, $p['region_id']);
    }

    // Search profiles by cities
    $city = City::query()->inRandomOrder()->first();
    $profiles = $this->get(route('api.profiles.search', ['city_id' => $city->id]))
      ->assertOk()
      ->json('result.data');

    foreach ($profiles as $profile) {
      $this->assertEquals($city->id, $profile['city_id']);
    }

    // Search profiles by districts
    $district = District::query()->inRandomOrder()->first();
    $profiles = $this->get(route('api.profiles.search', ['district_id' => $district->id]))
      ->assertOk()
      ->json('result.data');

    foreach ($profiles as $profile) {
      $this->assertEquals($district->id, $profile['district_id']);
    }

    // Search profile by keyword
    $category = Category::query()
      ->inRandomOrder()
      ->first();

    $keyword = substr($category->name, 1, 4);

    $profiles = $this->get(route('api.profiles.search', ['keyword' => $keyword]))
      ->assertOk()
      ->json('result.data');

    foreach ($profiles as $profile) {
      // Get all category ids from all specialities
      $catIds = array_reduce(
        $profile['specialities'],
        function ($acc, $s) {
          $categories = explode('|', $s['category_path']);
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
      $this->assertStringContainsString("|{$category->id}|", join(',',$spec));
    }
  }
}
