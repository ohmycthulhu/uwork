<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTextStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('text_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('key')
              ->unique();
            $table->unsignedInteger('upvotes')
              ->default(0);
            $table->unsignedInteger('downvotes')
              ->default(0);
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
        Schema::dropIfExists('text_statistics');
    }
}
