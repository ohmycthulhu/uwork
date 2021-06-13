<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSubwayForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subways', function (Blueprint $table) {
          $table->dropForeign('subways_city_id_foreign');
          $table->dropForeign('subways_district_id_foreign');

          $table->foreign('city_id')
            ->references('id')
            ->on('cities')
            ->cascadeOnDelete();

          $table->foreign('district_id')
            ->references('id')
            ->on('districts')
            ->nullOnDelete();
        });
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
