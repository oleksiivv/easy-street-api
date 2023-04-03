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
        /*
        'game_id',

        'google_play',
        'aptoide',
        'amazon_app_store',
        'galaxy_store',

        'app_store',
        'tweak_box',
        'cydia',

        'microsoft_store',
        'steam',
        'epic_games_store'
         */
        Schema::create('game_links', function (Blueprint $table) {
            $table->id();

            $table->string('google_play')->nullable();
            $table->string('app_store')->nullable();

            $table->string('aptoide')->nullable();
            $table->string('amazon_app_store')->nullable();
            $table->string('galaxy_app_store')->nullable();

            $table->string('tweak_box')->nullable();
            $table->string('cydia')->nullable();

            $table->string('microsoft_store')->nullable();
            $table->string('steam')->nullable();
            $table->string('epic_games_store')->nullable();

            $table->unsignedBigInteger('game_id');

            $table->foreign('game_id')
                ->references('id')
                ->on('games');

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
        Schema::dropIfExists('game_links');
    }
};
