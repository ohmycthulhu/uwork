<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppealsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('appeals', function (Blueprint $table) {
      $table->id();

      // Main information
      $table->text('text')
        ->nullable();
      $table->foreignId('appeal_reason_id')
        ->nullable()
        ->references('id')
        ->nullOnDelete()
        ->on('appeal_reasons');
      $table->string('appeal_reason_other')
        ->nullable();

      // Contact information
      $table->string('name');
      $table->string('email')->nullable();
      $table->string('phone')->nullable();
      $table->string('ip_addr')->nullable();
      $table->foreignId('user_id')
        ->nullable()
        ->references('id')
        ->nullOnDelete()
        ->on('users');

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
    Schema::dropIfExists('appeals');
  }
}
