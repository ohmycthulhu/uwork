<?php

use App\Models\Location\City;
use Illuminate\Database\Seeder;

class SubwaySeeder extends Seeder
{
  /* @var string $filePath */
  protected $filePath;

  /* @var City $city */
  protected $city;

  public function __construct()
  {
    $this->filePath = storage_path("data/subways.json");
    $this->city = new City;
  }

  /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subwayData = json_decode(\Illuminate\Support\Facades\File::get($this->filePath));

        foreach ($subwayData as $cityData) {
          /* @var ?City $city */
          $city = $this->city::query()
            ->find($cityData->city_id);

          if (!$city) {
            echo "City {$cityData->name} (#{$cityData->city_id}) not found\n";
            continue;
          }
          echo "Filling subways for {$cityData->name}\n";

          $linesCount = sizeof($cityData->lines);
          foreach ($cityData->lines as $li => $line) {
            if ($linesCount > 1) {
              echo "Line {$line->name} " . ($li + 1) . " / $linesCount\n";
            }
            foreach ($line->stations as $stationName) {
              if ($city->subways()
                ->where('name', $stationName)
                ->where('line', $line->name)
                ->first() == null
              ) {
                $city->subways()->create([
                  'name' => $stationName,
                  'line' => $line->name,
                  'color' => $line->color,
                ]);
              }
            }
          }
        }
    }
}
