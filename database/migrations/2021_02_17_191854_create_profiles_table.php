<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('profiles', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')
        ->unique()
        ->references('id')
        ->on('users')
        ->cascadeOnDelete();

      // Text
      $table->text('about');
      $table->string('phone');
      $table->boolean('phone_verified')->default(false);

      // Profile picture
//      $table->string('picture')->nullable();

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

      // Helping columns
      $table->boolean('is_hidden')
        ->default(false)
        ->index();

      $table->dateTime('confirmed_at')
        ->nullable();

      $table->boolean('failed_audition')
        ->default(false);

      $table->unsignedInteger('views_count')->default(0);
      $table->unsignedInteger('open_count')->default(0);
      $table->unsignedInteger('reviews_count')->default(0);
      $table->unsignedInteger('phone_display_count')->default(0);

      // Ratings
      $table->float('positive_rating_ratio')->default(0);
      $table->float('rating')->default(0);
      $table->float('rating_time')->default(0);
      $table->float('rating_quality')->default(0);
      $table->float('rating_price')->default(0);

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
    Schema::dropIfExists('profiles');
  }
}
