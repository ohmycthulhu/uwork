<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistrictsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('districts', function (Blueprint $table) {
      $table->id();
      $table->string('name', 256)->index();
      $table->foreignId('city_id')
        ->references('id')
        ->on('cities')
        ->cascadeOnDelete();
      $table->string('google_id')->nullable()->unique();
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
    Schema::dropIfExists('districts');
  }
}
