<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDuplicateRegions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      $regionQuery = \App\Models\Location\Region::query();
        $regionsNames = (clone $regionQuery)->groupBy('name')
          ->pluck('name');

        foreach ($regionsNames as $name) {
          (clone $regionQuery)
            ->skip(1)
            ->name($name)
            ->forceDelete();
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
