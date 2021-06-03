<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixColorColumnInSubways extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subways', function (Blueprint $table) {
            $table->dropColumn('color');
        });
        Schema::table('subways', function (Blueprint $table) {
            $table->string('color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subways', function (Blueprint $table) {
          $table->dropColumn('color');
        });
        Schema::table('subways', function (Blueprint $table) {
          $table->char('color', 9)->nullable();
        });
    }
}
