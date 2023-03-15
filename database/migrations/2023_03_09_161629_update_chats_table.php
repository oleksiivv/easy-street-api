<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('chats');

        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id');

            $table->foreign('game_id')
                ->references('id')
                ->on('games');

            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('chat_id');
            $table->string('message');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('chat_id')
                ->references('id')
                ->on('chats');

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
        //
    }
};
