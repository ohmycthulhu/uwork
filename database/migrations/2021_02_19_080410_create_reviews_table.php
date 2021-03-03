<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // Ratings columns
            $table->integer('rating_quality');
            $table->integer('rating_price');
            $table->integer('rating_time');

            $table->string('headline');
            $table->text('text');

            $table->foreignId('speciality_id')
              ->nullable()
              ->references('id')
              ->on('profile_specialities')
              ->nullOnDelete();

            $table->foreignId('profile_id')
              ->references('id')
              ->on('profiles')
              ->cascadeOnDelete();

            $table->foreignId('user_id')
              ->references('id')
              ->on('users')
              ->cascadeOnDelete();


            /**
             * Multiple columns that can be found useful
            */
            $table->string('ip_addr')->index()->nullable();

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
        Schema::dropIfExists('reviews');
    }
}
