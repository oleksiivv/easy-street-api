<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerGameController;
use App\Http\Controllers\PublisherGameController;
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
        Route::get('/', [PublisherGameController::class, 'index']);
        Route::get('/{id}', [PublisherGameController::class, 'getGame'])->whereNumber('id');

        Route::group(['prefix' => '/update'], function () {
            Route::put('/release', [PublisherGameController::class, 'updateGameRelease']);
            Route::put('/security', [PublisherGameController::class, 'updateGameSecurity']);
            Route::put('/page', [PublisherGameController::class, 'updateGamePage']);
            Route::put('/category', [PublisherGameController::class, 'updateGameCategory']);
        });
    });

    Route::group(['prefix' => '/company'], function () {
        Route::get('/', [CompanyController::class, 'index']);
        Route::get('/{id}', [CompanyController::class, 'get']);
        Route::post('/', [CompanyController::class, 'create']);
        Route::put('/', [CompanyController::class, 'update']);

        Route::group(['prefix' => '/team'], function () {
            Route::get('/', [CompanyController::class, 'getTeam']);
            Route::post('/team-member', [CompanyController::class, 'addTeamMember']);
            Route::delete('/team-member', [CompanyController::class, 'removeTeamMember']);
        });
    });
});

Route::group(['prefix' => '/customer'], function () {
    Route::group(['prefix' => '/game'], function () {
        Route::get('/', [CustomerGameController::class, 'index']);
        Route::get('/{id}', [CustomerGameController::class, 'getGame'])->whereNumber('id');
        Route::patch('/{id}/add-to-favourites', [CustomerGameController::class, 'addToFavourites'])->whereNumber('id');
        Route::patch('/{id}/remove-from-favourites', [CustomerGameController::class, 'removeFromFavourites'])->whereNumber('id');
        Route::patch('/{id}/download', [CustomerGameController::class, 'download'])->whereNumber('id');
        Route::patch('/{id}/remove', [CustomerGameController::class, 'remove'])->whereNumber('id');
    });

    Route::get('/downloaded-now', [CustomerGameController::class, 'getDownloaded']);
    Route::get('/favourites', [CustomerGameController::class, 'getFavourites']);
    Route::get('/downloaded-all-time', [CustomerGameController::class, 'getAllTimeDownloaded']);
});
