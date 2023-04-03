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
        Schema::create('game_actions', function (Blueprint $table) {
            $table->id();

            $table->string('type')->default('update');
            $table->json('fields')->nullable(false);

            $table->string('performed_by')->default('company');

            $table->unsignedBigInteger('game_id');
            $table->unsignedBigInteger('user_id')->nullable();

            $table->foreign('game_id')
                ->references('id')
                ->on('games');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

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
        Schema::dropIfExists('game_actions');
    }
};
