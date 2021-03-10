<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileSpecialitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_specialities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('profile_id')
              ->references('id')
              ->on('profiles')
              ->cascadeOnDelete();

            $table->foreignId('category_id')
              ->references('id')
              ->on('categories')
              ->cascadeOnDelete()
              ->cascadeOnUpdate();

            $table->string('name')->nullable()->index();

            $table->float('price')->index();

            $table->string('category_path')
              ->index()
              ->nullable();

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
        Schema::dropIfExists('profile_specialities');
    }
}
