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
      $name = $regionInfo->name ?? null;

      $region = Region::query()
        ->name($name)
        ->first();
      if ($region) {
        echo "Region ({$name}) already exists\n";
      }

      $region = $region ??
        Region::create([
          'name' => $regionInfo->name,
          'google_id' => $regionInfo->google_id ?? null,
        ]);

      // Create cities for each region
      $citiesList = $regionInfo->cities;
      foreach ($citiesList as $cityInfo) {
        $city = $region->cities()->name($cityInfo->name)->first();

        if ($city) {
          echo "City ({$cityInfo->name}) already exists\n";
        }

        $city = $city ??
          $region->cities()->create([
            'name' => $cityInfo->name,
            'google_id' => $cityInfo->google_id ?? null,
          ]);

        // Create district for each city
        $districtsInfo = $cityInfo->districts ?? [];
        $this->createDistricts($city, $districtsInfo);

        // Create subway for each city
        $subwayInfo = $cityInfo->subway ?? null;
        if ($subwayInfo) {
          $this->createSubways($city, $subwayInfo);
        }
      }
    }
  }

  public function createDistricts(City $city, array $districts)
  {
    foreach ($districts as $districtInfo) {
      $districtName = $districtInfo->name;
      if (!$city->districts()->name($districtName)->first()) {
        $city->districts()->create([
          'name' => $districtName,
        ]);
      }
    }
  }

  public function createSubways(City $city, array $lines)
  {
    foreach ($lines as $line) {
      $lineName = $line->name;
      $lineColor = $line->color;
      foreach ($line->stations as $station) {
        $existingSubway = $city->subways()
          ->name($station)
          ->line($lineName)
          ->first();
        if (!$existingSubway) {
          echo "Creating subway $station\n";
          $city->subways()
            ->create([
              'name' => $station,
              'line' => $lineName,
              'color' => $lineColor
            ]);
        }
      }
    }
  }
}
