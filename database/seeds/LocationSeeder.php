<?php

use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // Create regions
    $regions = factory(\App\Models\Location\Region::class, 5)->create();

    // For each regions, create cities
    foreach ($regions as $region) {
      $cities = $region->cities()
        ->createMany(
          factory(\App\Models\Location\City::class, 5)
            ->make()
            ->toArray()
        );

      // For each city, create districts
      foreach ($cities as $city) {
        $city->districts()
          ->createMany(factory(\App\Models\Location\District::class, 3)->make()->toArray());
      }
    }
  }
}
