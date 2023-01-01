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
        Schema::create('customer_games', function (Blueprint $table) {
            $table->id();
            $table->boolean('downloaded')->default(false);
            $table->boolean('favourite')->default(false);
            $table->dateTime('download_datetime')->nullable()->default(null);

            $table->unsignedBigInteger('game_id');
            $table->unsignedBigInteger('user_id');

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
        Schema::dropIfExists('customer_games');
    }
};
