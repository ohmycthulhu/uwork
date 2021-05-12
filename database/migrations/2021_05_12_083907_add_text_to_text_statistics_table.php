<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTextToTextStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('text_statistics', function (Blueprint $table) {
            $table->string('name')
              ->nullable();
            $table->longText('text')
              ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('text_statistics', function (Blueprint $table) {
            $table->dropColumn(['text', 'name']);
        });
    }
}
