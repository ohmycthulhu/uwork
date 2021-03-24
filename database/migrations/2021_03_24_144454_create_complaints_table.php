<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();

            $table->foreignId('type_id')
              ->nullable()
              ->references('id')
              ->on('complaint_types')
              ->nullOnDelete();

            $table->foreignId('user_id')
              ->nullable()
              ->references('id')
              ->on('users')
              ->nullOnDelete();

            $table->string('ip_addr')
              ->nullable()
              ->index();

            $table->string('reason_other')
              ->nullable();

            $table->text('text');

            $table->morphs('complaintable');

            $table->boolean('is_open')
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
        Schema::dropIfExists('complaints');
    }
}
