<?php

use App\Models\Game;
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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->json('permissions')->nullable();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');

            $table->string('email');
            $table->string('email_confirmation_token')->nullable();
            $table->boolean('email_is_confirmed')->default(false);

            $table->string('password_sha');
            $table->string('update_password_token')->nullable();

            $table->unsignedBigInteger('role_id');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('site');
            $table->string('phone_number');
            $table->unsignedBigInteger('publisher_id');
            $table->json('team_members');

            $table->foreign('publisher_id')
                ->references('id')
                ->on('users');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('game_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');

            $table->unsignedBigInteger('company_id');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('genre', Game::GENRES);
            $table->enum('status', Game::STATUSES);
            $table->json('tags');
            $table->string('site')->nullable();

            $table->unsignedBigInteger('game_category_id')->nullable();
            $table->unsignedBigInteger('company_id');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies');

            $table->foreign('game_category_id')
                ->references('id')
                ->on('game_categories');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('game_pages', function (Blueprint $table) {
            $table->id();
            $table->string('short_description');
            $table->string('long_description');
            $table->string('icon_url');
            $table->string('background_image_url');
            $table->json('description_images');

            $table->unsignedBigInteger('game_id');

            $table->foreign('game_id')
                ->references('id')
                ->on('games');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('game_securities', function (Blueprint $table) {
            $table->id();
            $table->boolean('has_ads');
            $table->json('ads_providers');
            $table->string('privacy_policy_url');
            $table->integer('minimum_age');
            $table->json('sensitive_content');

            $table->unsignedBigInteger('game_id');

            $table->foreign('game_id')
                ->references('id')
                ->on('games');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('game_releases', function (Blueprint $table) {
            $table->id();
            $table->string('version');
            $table->string('android_file_url')->nullable();
            $table->string('ios_file_url')->nullable();
            $table->string('windows_file_url')->nullable();
            $table->string('mac_file_url')->nullable();
            $table->string('linux_file_url')->nullable();

            $table->unsignedBigInteger('game_id');

            $table->foreign('game_id')
                ->references('id')
                ->on('games');

            $table->timestamp('release_date')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('paid_products', function (Blueprint $table) {
            $table->id();
            $table->integer('price');
            $table->string('currency');

            $table->unsignedBigInteger('game_id');

            $table->foreign('game_id')
                ->references('id')
                ->on('games');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
        Schema::dropIfExists('companies');

        Schema::dropIfExists('game_pages');
        Schema::dropIfExists('game_securities');
        Schema::dropIfExists('game_releases');
        Schema::dropIfExists('game_categories');
        Schema::dropIfExists('games');
    }
};
