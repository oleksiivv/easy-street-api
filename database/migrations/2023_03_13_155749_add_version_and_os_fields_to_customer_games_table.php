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
        Schema::table('customer_games', function (Blueprint $table) {
            $table->string('version')->nullable();
            $table->string('os')->default('android');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_games', function (Blueprint $table) {
            $table->dropColumn('version', 'os');
        });
    }
};
