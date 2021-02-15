<?php

namespace Tests\Feature;

use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class LocationTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Method to test database actions
   *
   * @return void
  */
  public function testDatabase() {
    // Create regions
    $region = factory(Region::class)->create();

    $this->assertPostConditions();

    // Create cities
    $cities = $region->cities()->createMany(factory(City::class, 5)->make()->toArray());

    $this->assertPostConditions();

    // Create districts
    foreach ($cities as $city) {
      $city->districts()->createMany(factory(District::class, 5)->make()->toArray());
    }

    // Ensure count of regions, cities and districts
    $this->assertDatabaseCount($region->getTable(), 1);
    $this->assertDatabaseCount($cities[0]->getTable(), 5);
    $this->assertDatabaseCount($cities[0]->districts()->first()->getTable(), 25);

    // Delete some cities
    $cities[1]->forceDelete();
    $this->assertPostConditions();

    // Delete regions
    $region->forceDelete();
    $this->assertPostConditions();
  }

  /**
   * Method to test endpoints
   *
   * @return void
  */
  public function testAPI() {
    // Set up database
    $this->setUpDatabase();

    // Get regions
    $regionsCount = Region::query()->count();

    // Ensure regions' count is correct
    $this->checkRegionsRoute($regionsCount);

    // Get regions from database
    $regions = Region::all();

    foreach ($regions as $region) {
      // Get information about each region
      // Get cities for each region
      $cities = $this->get($region->link)
        ->assertOk()
        ->json('region.cities');

      $this->assertEquals(sizeof($cities), $region->cities()->count());
    }

    // Get information about each city
    $cities = City::all();

    foreach ($cities as $city) {
      // Ensure district count is correct
      $districts = $this->get($city->linkDistricts)
        ->assertOk()
        ->json('districts');

      $this->get($city->link)
          ->assertOk();

      $this->assertEquals($city->districts()->count(), sizeof($districts));
    }

    // Clear database
    $this->clearDatabase();
  }

  /**
   * Method to check if regions are loaded correctly
   *
   * @param int $count
   *
  */
  protected function checkRegionsRoute(int $count) {
    // Send request to regions
    $regions = $this->get(route('api.regions.all'))
      ->assertOk()
      ->json('regions');

    // Check count of regions
    $this->assertEquals($count, sizeof($regions));
  }

  /**
   * Method to set up database and create locations
   *
   * @return Collection
  */
  protected function setUpDatabase(): Collection {
    $regions = factory(Region::class, 5)->create();

    foreach ($regions as $region) {
      $cities = $region->cities()
          ->createMany(
            factory(City::class, 5)
              ->make()
              ->toArray()
          );

      foreach ($cities as $city) {
        $city->districts()
          ->createMany(
            factory(
              District::class, 5
            )->make()
              ->toArray()
          );
      }
    }

    return $regions;
  }

  /**
   * Method to clear database and remove everything related to location
   *
   * @return void
  */
  protected function clearDatabase() {
    Region::query()->forceDelete();
  }
}
