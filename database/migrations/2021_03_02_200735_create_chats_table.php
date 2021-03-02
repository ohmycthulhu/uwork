<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('initiator_id')
              ->nullable()
              ->references('id')
              ->on('users')
              ->nullOnDelete()
              ->cascadeOnUpdate();

            $table->foreignId('acceptor_id')
              ->nullable()
              ->references('id')
              ->on('users')
              ->nullOnDelete()
              ->cascadeOnUpdate();

            $table->dateTime('last_message_time')
              ->nullable();

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
        Schema::dropIfExists('chats');
    }
}
