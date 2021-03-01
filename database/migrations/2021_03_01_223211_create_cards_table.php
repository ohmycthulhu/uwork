<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->integer('expiration_month');
            $table->integer('expiration_year');
            $table->string('cvv');
            $table->string('name');
            $table->string('label')->nullable();

            $table->foreignId('user_id')
              ->nullable()
              ->references('id')
              ->on('users')
              ->nullOnDelete()
              ->cascadeOnUpdate();

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
        Schema::dropIfExists('cards');
    }
}
