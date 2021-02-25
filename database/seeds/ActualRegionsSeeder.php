<?php

use App\Models\Location\City;
use App\Models\Location\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ActualRegionsSeeder extends Seeder
{
  /**
   * File contains list of cities
   *
   * @var string
   */
  protected $file;

  /**
   * Creates instance of seeder
   *
   * @param ?string $path
   */
  public function __construct(?string $path = null)
  {
    $this->file = $path ?? storage_path('data/regions.json');
  }

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // Read regions from file
    $jsonData = json_decode(File::get($this->file));
    $regionsData = $jsonData->regions;

    // Create regions for each entry
    foreach ($regionsData as $regionInfo) {
      $googleId = $regionInfo->google_id;

      $region = Region::query()->googleId($googleId)->first();
      if ($region) {
        echo "City ({$regionInfo->name}) already exists\n";
      }

      $region = $region ??
        Region::create([
          'name' => $regionInfo->name,
          'google_id' => $googleId,
        ]);

      // Create cities for each region
      $citiesList = $regionInfo->cities;
      foreach ($citiesList as $cityInfo) {
        $cityGoogleId = $cityInfo->google_id;

        $city = City::query()->googleId($cityGoogleId)->first();
        if ($city) {
          echo "City ({$cityInfo->name}) already exists\n";
        }

        $city = $city ??
          $region->cities()->create([
            'name' => $cityInfo->name,
            'google_id' => $cityGoogleId,
          ]);

        // Create district for each city
        $districtsInfo = $cityInfo->districts;
        foreach ($districtsInfo as $districtInfo) {
          $districtName = $districtInfo->name;
          if (!$city->districts()->name($districtName)->first()) {
            $city->districts()->create([
              'name' => $districtName,
            ]);
          }
        }
      }
    }
  }
}
