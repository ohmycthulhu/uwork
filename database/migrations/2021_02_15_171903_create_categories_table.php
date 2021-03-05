<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('categories', function (Blueprint $table) {
      $table->id();
      $table->string('name', 256)->index();
      $table->string('slug', 256)->index()->nullable();

      $table->foreignId('parent_id')
        ->nullable()
        ->references('id')
        ->on('categories')
        ->cascadeOnDelete();

      // Icons
      $table->string('icon_default')
        ->nullable();
      $table->string('icon_selected')
        ->nullable();

      $table->boolean('is_baseline')
        ->default(false);

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
    Schema::dropIfExists('categories');
  }
}
