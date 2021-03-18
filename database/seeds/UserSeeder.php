<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $regions = \App\Models\Location\Region::all()->load(['cities.districts']);
      for ($i = 0; $i < 10; $i++) {
        $region = $regions->random();
        $city = $region->cities->random();
        $district = $city->districts()->random();
        factory(\App\Models\User::class)->create([
          'region_id' => $region->id,
          'city_id' => $city->id,
          'district' => $district ? $district->id : null,
        ]);
      }
    }
}
