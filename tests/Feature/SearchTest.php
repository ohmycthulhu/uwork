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

    $keyword = substr($category->name, 0, 4);

    $profiles = $this->get(route('api.profiles.search', ['keyword' => $keyword]))
      ->assertOk()
      ->json('result.data');

    foreach ($profiles as $profile) {
      $this->assertStringContainsStringIgnoringCase(
        $keyword,
        join('|', array_map(function ($spec) {
            return join(', ', array_values($spec['category']['name']));
        }, $profile['specialities']))
      );
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

  /**
   * Method to fill database
   *
   * @return void
   */
  protected function fillDatabase()
  {
    $categories = $this->createCategories();
    $regions = $this->createRegions();

    for ($i = 0; $i < 10; $i++) {
      $user = $this->createUser();
      $profile = $this->createProfile($user);
      $profile->specialities()
        ->createMany(
          $categories->shuffle()
            ->take(3)
            ->map(function ($c) {
              return factory(ProfileSpeciality::class)
                ->make(['category_id' => $c['id']]);
            })
            ->toArray()
        );
      $region = $regions->random();
      $city = $region->cities->random();
      $district = rand() % 2 === 0 ? $city->districts->random() : null;

      $profile->region_id = $region->id;
      $profile->city_id = $city->id;
      $profile->district_id = $district ? $district->id : null;
      $profile->save();
    }
  }

  /**
   * Method to create categories
   *
   * @return Collection
   */
  protected function createCategories(): Collection
  {
    $categories = factory(Category::class, 4)->create();
    $categories->push(
      ...$categories->reduce(function ($acc, $c) {
      return array_merge(
        $acc,
        $c->children()
          ->createMany(factory(Category::class, 3)->make()->toArray())
          ->toArray()
      );
    }, [])
    );
    return $categories;
  }

  /**
   * Method to create regions
   *
   * @return Collection
   */
  protected function createRegions(): Collection
  {
    /* Fill regions */
    $regions = factory(Region::class, 4)
      ->create();
    foreach ($regions as $region) {
      $cities = $region->cities()
        ->createMany(
          factory(City::class, 5)
            ->make()
            ->toArray()
        );

      foreach ($cities as $city) {
        $districts = $city->districts()
          ->createMany(
            factory(District::class, 6)
            ->make()
            ->toArray()
          );

        $city->districts = $districts;
      }

      $region->cities = $cities;
    }

    return $regions;
  }
}
