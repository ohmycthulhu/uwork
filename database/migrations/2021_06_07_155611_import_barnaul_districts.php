<?php

use App\Models\Location\City;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ImportBarnaulDistricts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $data = json_decode(File::get(storage_path("data/regions.json")), true);
        $region = array_values(array_filter($data['regions'], function ($r) {
          return $r['id'] == '8276c6a1-1a86-4f0d-8920-aba34d4cc34a';
        }))[0];
        $city = array_values(array_filter($region['cities'], function ($c) {
          return $c['id'] == 'd13945a8-7017-46ab-b1e6-ede1e89317ad';
        }))[0];
        $districts = array_map(function ($d) { return $d['name']; }, $city['districts']);
        $c = City::query()
          ->where('name', $city['name'])
          ->first();

        $c->districts()
          ->whereNotIn('name', $districts)
          ->forceDelete();

        /* @var \Illuminate\Support\Collection $existing */
        $existing = $c->districts()->whereIn('name', $districts)->pluck('name');

        foreach ($districts as $district) {
          if (!$existing->contains($district)) {
            $c->districts()->create(['name' => $district]);
          }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
