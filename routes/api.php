<?php

use App\Http\Controllers\PublisherGameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => '/publisher'], function () {
    Route::group(['prefix' => '/game'], function () {
        Route::post('/', [PublisherGameController::class, 'createGame']);
        Route::put('/', [PublisherGameController::class, 'updateGame']);

        Route::group(['prefix' => '/update'], function () {
            Route::put('/release', [PublisherGameController::class, 'updateGameRelease']);
            Route::put('/security', [PublisherGameController::class, 'updateGameSecurity']);
            Route::put('/page', [PublisherGameController::class, 'updateGamePage']);
            Route::put('/category', [PublisherGameController::class, 'updateGameCategory']);
        });
    });
});
