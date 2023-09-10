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
        Schema::create('ads_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();

            $table->unsignedBigInteger('admin_id');

            $table->foreign('admin_id')
                ->references('id')
                ->on('administrators_to_moderators_pivot');

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
        Schema::dropIfExists('ads_subscribers');
    }
};
