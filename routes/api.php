<?php

use App\Http\Controllers\AccountPaymentCardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerGameController;
use App\Http\Controllers\CustomerPublishersController;
use App\Http\Controllers\PublisherGameController;
use App\Http\Controllers\PublisherGamePageController;
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

Route::group(['prefix' => '/publisher', 'middleware' => 'publisher-access'], function () {
    Route::group(['prefix' => '/game'], function () {
        Route::post('/', [PublisherGameController::class, 'createGame']);
        Route::put('/{id}', [PublisherGameController::class, 'updateGame']);
        Route::put('/{id}/release', [PublisherGameController::class, 'releaseGame']);
        Route::get('/', [PublisherGameController::class, 'index']);

        Route::get('/{id}/stats', [PublisherGameController::class, 'gameStats'])->whereNumber('id');
        Route::get('/{id}', [PublisherGameController::class, 'getGame'])->whereNumber('id');

        Route::group(['prefix' => '/{gameId}/update'], function () {
            Route::put('/release', [PublisherGameController::class, 'updateGameRelease']);
            Route::post('/release/files', [PublisherGameController::class, 'updateGameReleaseFiles']);
            Route::put('/security', [PublisherGameController::class, 'updateGameSecurity']);
            Route::put('/page', [PublisherGameController::class, 'updateGamePage']);
            Route::put('/product', [PublisherGameController::class, 'updateGameProduct']);
            Route::put('/category', [PublisherGameController::class, 'updateGameCategory']);
        });

        Route::group(['prefix' => '/{id}'], function () {
            Route::group(['prefix' => '/page'], function () {
                Route::group(['prefix' => '/upload'], function () {
                    Route::post('/icon', [PublisherGamePageController::class, 'uploadIcon']);
                    Route::post('/background', [PublisherGamePageController::class, 'uploadBackground']);
                    Route::post('/description', [PublisherGamePageController::class, 'uploadDescriptionImages']);
                });
            });
        });
    });

    Route::group(['prefix' => '/company'], function () {
        Route::get('/', [CompanyController::class, 'index']);
        Route::get('/{id}', [CompanyController::class, 'get']);
        Route::get('/{id}/stats', [CompanyController::class, 'stats']);
        Route::get('/{id}/subscribers', [CompanyController::class, 'subscribers']);
        Route::post('/', [CompanyController::class, 'create']);
        Route::put('/{id}', [CompanyController::class, 'update']);

        Route::group(['prefix' => '/{id}/team'], function () {
            Route::get('/', [CompanyController::class, 'getTeam']);
            Route::post('/team-member', [CompanyController::class, 'addTeamMember']);
            Route::delete('/team-member', [CompanyController::class, 'removeTeamMember']);
        });

        Route::get('/{id}/categories', [PublisherGameController::class, 'getCategories'])->whereNumber('id');
    });
});

Route::group(['prefix' => '/customer'], function () {
    Route::get('/{id}/subscriptions', [CustomerPublishersController::class, 'subscriptions'])->whereNumber('id');

    Route::group(['prefix' => '/game'], function () {
        Route::get('/', [CustomerGameController::class, 'index']);
        Route::get('/{id}', [CustomerGameController::class, 'getGame'])->whereNumber('id');
        Route::get('/{id}/stats', [CustomerGameController::class, 'gameStats'])->whereNumber('id');
        Route::get('/{id}/os', [CustomerGameController::class, 'getAvailableOSs'])->whereNumber('id');
        Route::post('/{id}/download', [CustomerGameController::class, 'download'])->whereNumber('id');

        Route::get('/search/{keyword}', [CustomerGameController::class, 'search']);

        Route::group(['middleware' => 'customer-access'], function () {
            Route::get('/{id}/stats/user/{userId}', [CustomerGameController::class, 'gameStatsForUser'])->whereNumber('id')->whereNumber('userId');

            Route::patch('/{id}/add-to-favourites', [CustomerGameController::class, 'addToFavourites'])->whereNumber('id');
            Route::patch('/{id}/remove-from-favourites', [CustomerGameController::class, 'removeFromFavourites'])->whereNumber('id');
            Route::post('/{id}/rate', [CustomerGameController::class, 'rate'])->whereNumber('id');
            Route::patch('/{id}/remove', [CustomerGameController::class, 'remove'])->whereNumber('id');
        });
    });

    Route::group(['prefix' => '/publisher'], function () {
        Route::get('/', [CustomerPublishersController::class, 'index']);
        Route::get('/{id}/stats', [CustomerPublishersController::class, 'stats'])->whereNumber('id');
        Route::post('/{id}/subscribe-unsubscribe', [CustomerPublishersController::class, 'subscribeOrUnsubscribe'])->whereNumber('id');
        Route::get('/{id}/subscribed', [CustomerPublishersController::class, 'isSubscribed'])->whereNumber('id');
    });

    Route::get('/downloaded-now', [CustomerGameController::class, 'getDownloaded']);
    Route::get('/favourites', [CustomerGameController::class, 'getFavourites']);
    Route::get('/downloaded-all-time', [CustomerGameController::class, 'getAllTimeDownloaded']);

    Route::get('/{id}', [CustomerController::class, 'show']);
});

Route::group(['prefix' => '/my-account'], function () {
    Route::post('/login', [AccountController::class, 'login']);
    Route::post('/logout', [AccountController::class, 'logout']);
    Route::post('/try-login-via-cache', [AccountController::class, 'tryLoginViaCache']);

    Route::post('/register', [AccountController::class, 'register']);
    Route::get('/confirm-email/{email}/{emailConfirmationToken}', [AccountController::class, 'confirmEmail'])->middleware('customer-access');

    Route::put('/{id}/update', [AccountController::class, 'update'])->middleware('customer-access');

    Route::post('/forgot-password', [AccountController::class, 'forgotPassword']);
    Route::get('/confirm-new-password/{email}/{newPasswordSha}/{confirmNewPasswordToken}', [AccountController::class, 'confirmNewPassword']);

    Route::group(['prefix' => '/{id}/card'], function (){
        Route::get('/', [AccountPaymentCardController::class, 'index']);
        Route::post('/', [AccountPaymentCardController::class, 'add']);
        Route::delete('/all', [AccountPaymentCardController::class, 'deleteAll']);
        Route::delete('/{cardId}', [AccountPaymentCardController::class, 'delete']);
    });
    Route::get('/billing-portal', function (Request $request) {
        return $request->user()->redirectToBillingPortal();
    })->middleware('customer-access');
});

Route::group(['prefix' => '/test'], function () {
    Route::get('sendbasicemail', [\App\Http\Controllers\MailController::class, 'basicEmail']);
    Route::get('sendhtmlemail',  [\App\Http\Controllers\MailController::class, 'html_email']);

    Route::group(['prefix' => '/stripe'], function () {
        Route::get('/', [\App\Http\Controllers\StripePaymentController::class, 'stripe']);
        Route::post('/pay', [\App\Http\Controllers\StripePaymentController::class, 'stripePost']);
    });
});

Route::group(['prefix' => '/chat'], function () {
    Route::post('/', [\App\Http\Controllers\Chat\ChatController::class, 'create'])->middleware('publisher-access');

    Route::get('/game/{gameId}', [\App\Http\Controllers\Chat\ChatController::class, 'getByGameId'])->middleware('customer-access');
    Route::get('/game/{gameId}/id', [\App\Http\Controllers\Chat\ChatController::class, 'getChatIdByGameId'])->middleware('customer-access');

    Route::group(['prefix' => '/{id}'], function () {
        Route::get('/', [\App\Http\Controllers\Chat\ChatController::class, 'index'])->middleware('customer-access');

        Route::group(['prefix' => '/message'], function () {
           Route::post('/', [\App\Http\Controllers\Chat\ChatMessageController::class, 'sendMessage'])->middleware('customer-access'); //send
       });
    });
});

Route::group(['prefix' => '/moderator', 'middleware' => 'moderator-access'], function () {
    Route::group(['prefix' => '/games'], function () {
        Route::get('/', [\App\Http\Controllers\Administration\ModeratorController::class, 'index']);

        Route::group(['prefix' => '/{gameId}'], function () {
            Route::get('/', [\App\Http\Controllers\Administration\ModeratorController::class, 'getGame']);
            Route::put('/', [\App\Http\Controllers\Administration\ModeratorController::class, 'updateGame']);
        });
    });
});

Route::group(['prefix' => '/admin', 'middleware' => 'admin-access'], function () {
    Route::group(['prefix' => '/{id}'], function () {
        Route::get('/', [\App\Http\Controllers\Administration\AdministratorController::class, 'getData']);

        Route::group(['prefix' => '/moderators'], function () {
            Route::post('/', [\App\Http\Controllers\Administration\AdministratorController::class, 'createModerator']);
        });
    });
});
