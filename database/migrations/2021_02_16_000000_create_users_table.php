<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('first_name');
      $table->string('last_name');
      $table->string('father_name');
      $table->string('email')->nullable()->unique();
      $table->string('phone')->unique();
//      $table->boolean('phone_verified')->default(false);
      $table->string('password');

      // Settings section
      $table->text('settings')->nullable();

      // Detailed information
      $table->string('avatar')->nullable();
      $table->date('birthdate')->nullable();

      // Location
      $table->foreignId('region_id')
        ->nullable()
        ->references('id')
        ->on('regions')
        ->nullOnDelete();

      $table->foreignId('city_id')
        ->nullable()
        ->references('id')
        ->on('cities')
        ->nullOnDelete();

      $table->foreignId('district_id')
        ->nullable()
        ->references('id')
        ->on('districts')
        ->nullOnDelete();

      $table->boolean('is_male')
        ->default(true);

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
    Schema::dropIfExists('users');
  }
}
