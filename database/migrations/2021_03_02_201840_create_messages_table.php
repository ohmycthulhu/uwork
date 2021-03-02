<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->text('text')->nullable();
            $table->string('attachment')->nullable();

            $table->foreignId('user_id')
              ->nullable()
              ->references('id')
              ->on('users')
              ->nullOnDelete()
              ->cascadeOnUpdate();

            $table->foreignId('chat_id')
              ->references('id')
              ->on('chats')
              ->cascadeOnUpdate()
              ->cascadeOnDelete();

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
        Schema::dropIfExists('messages');
    }
}
