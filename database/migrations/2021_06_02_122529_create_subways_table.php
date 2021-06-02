<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubwaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->char('color', 9)->nullable();
            $table->char('identifier', 10)->nullable();

            $table->foreignId('city_id')
              ->nullable()
              ->references('id')
              ->on('cities');

            $table->foreignId('district_id')
              ->nullable()
              ->references('id')
              ->on('districts');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subways');
    }
}
