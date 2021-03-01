<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFavouriteServicesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_favourite_services', function (Blueprint $table) {
      $table->id();

      $table->foreignId('user_id')
        ->references('id')
        ->on('users')
        ->cascadeOnUpdate()
        ->cascadeOnDelete();
      $table->foreignId('service_id')
        ->references('id')
        ->on('profile_specialities')
        ->cascadeOnDelete()
        ->cascadeOnUpdate();

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
    Schema::dropIfExists('user_favourite_services');
  }
}
