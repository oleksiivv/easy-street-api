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
        Schema::table('game_releases', function (Blueprint $table) {
            $table->string('android_icon')->default('https://www.freeiconspng.com/thumbs/android-icon/android-robot-icon-22.png');
            $table->string('ios_icon')->default('https://www.freeiconspng.com/thumbs/ios-png/os7-style-metro-ui-icon-19.png');
            $table->string('windows_icon')->default('https://www.freeiconspng.com/thumbs/windows-icon-png/cute-ball-windows-icon-png-16.png');
            $table->string('mac_icon')->default('https://www.freeiconspng.com/thumbs/ios-png/os7-style-metro-ui-icon-19.png');
            $table->string('linux_icon')->default('https://static.vecteezy.com/system/resources/previews/014/441/176/original/folder-icon-file-quality-shape-design-free-png.png');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_releases', function (Blueprint $table) {
            $table->dropColumn(
                'android_icon',
                'ios_icon',
                'windows_icon',
                'mac_icon',
                'linux_icon'
            );
        });
    }
};
