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

Route::post('/publisher/company', [CompanyController::class, 'create']);

Route::group(['prefix' => '/publisher'], function () {
    Route::group(['prefix' => '/game', 'middleware' => 'publisher-access'], function () {
        Route::post('/', [PublisherGameController::class, 'createGame']);
        Route::put('/{id}', [PublisherGameController::class, 'updateGame']);
        Route::put('/{id}/release', [PublisherGameController::class, 'releaseGame']);
        Route::put('/{id}/demo', [PublisherGameController::class, 'releaseGameAsDemo']);
        Route::put('/{id}/draft', [PublisherGameController::class, 'makeDraft']);
        Route::get('/', [PublisherGameController::class, 'index']);

        Route::get('/{id}/stats', [PublisherGameController::class, 'gameStats'])->whereNumber('id');
        Route::get('/{id}', [PublisherGameController::class, 'getGame'])->whereNumber('id');

        Route::group(['prefix' => '/{gameId}/update'], function () {
            Route::put('/release', [PublisherGameController::class, 'updateGameRelease']);
            Route::put('/links', [PublisherGameController::class, 'updateGameLinks']);
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
        Route::get('/', [CompanyController::class, 'index'])->middleware('publisher-access');
        Route::get('/{id}', [CompanyController::class, 'get'])->middleware('publisher-access');
        Route::get('/{id}/stats', [CompanyController::class, 'stats'])->middleware('publisher-access');
        Route::get('/{id}/subscribers', [CompanyController::class, 'subscribers'])->middleware('publisher-access');
        Route::post('/', [CompanyController::class, 'create']);
        Route::put('/{id}', [CompanyController::class, 'update'])->middleware('publisher-access');

        Route::get('/team-member/{id}', [CompanyController::class, 'companiesByTeamMember'])->middleware('customer-access');

        Route::get('/game/{gameId}/actions', [\App\Http\Controllers\PublisherGameActionsController::class, 'allPublisherActionsForGame'])->middleware('publisher-access');
        Route::get('/game/{gameId}/actions/all', [\App\Http\Controllers\PublisherGameActionsController::class, 'allForGame'])->middleware('publisher-access');

        Route::group(['prefix' => '/{id}/team'], function () {
            Route::get('/', [CompanyController::class, 'getTeam'])->middleware('publisher-access');
            Route::post('/team-member', [CompanyController::class, 'addTeamMember'])->middleware('publisher-access');
            Route::delete('/team-member', [CompanyController::class, 'removeTeamMember'])->middleware('publisher-access');
        });

        Route::get('/{id}/categories', [PublisherGameController::class, 'getCategories'])->whereNumber('id');
        Route::get('/{id}/genres', [PublisherGameController::class, 'getGenres'])->whereNumber('id');
    });
});

Route::group(['prefix' => '/customer'], function () {
    Route::get('/{id}/subscriptions', [CustomerPublishersController::class, 'subscriptions'])->whereNumber('id');

    Route::group(['prefix' => '/game'], function () {
        Route::get('/', [CustomerGameController::class, 'index']);
        Route::get('/categories', [CustomerGameController::class, 'categories']);
        Route::get('/recommendations', [CustomerPublishersController::class, 'recomendations']);
        Route::get('/genres', [CustomerGameController::class, 'groupGamesByGenres']);
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
    Route::get('/confirm-email/{email}/{emailConfirmationToken}', [AccountController::class, 'confirmEmail']);//->middleware('customer-access');

    Route::put('/{id}/update', [AccountController::class, 'update'])->middleware('customer-access');

    Route::post('/forgot-password', [AccountController::class, 'forgotPassword']);
    Route::get('/confirm-new-password/{email}/{newPasswordSha}/{confirmNewPasswordToken}', [AccountController::class, 'confirmNewPassword']);

    Route::group(['prefix' => '/{id}/card'], function (){
        Route::get('/', [AccountPaymentCardController::class, 'index'])->middleware('customer-access');
        Route::get('/default', [AccountPaymentCardController::class, 'getDefault'])->middleware('customer-access');
        Route::post('/', [AccountPaymentCardController::class, 'add'])->middleware('customer-access');
        Route::put('/{cardId}/make-default', [AccountPaymentCardController::class, 'makeDefault'])->middleware('customer-access');
        Route::delete('/all', [AccountPaymentCardController::class, 'deleteAll'])->middleware('customer-access');
        Route::delete('/{cardId}', [AccountPaymentCardController::class, 'delete'])->middleware('customer-access');
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
    Route::get('/all', [\App\Http\Controllers\Chat\ChatController::class, 'activeChats']);

    Route::get('/user/{userId}', [\App\Http\Controllers\Chat\ChatController::class, 'getByUserId'])->middleware('customer-access');

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
            Route::put('/es-index', [\App\Http\Controllers\Administration\ModeratorController::class, 'updateGameESIndex']);

            Route::get('/actions/{moderatorId}', [\App\Http\Controllers\Administration\ModeratorGameActionsController::class, 'allForModeratorByGame']);
        });
    });

    Route::group(['prefix' => '{moderatorId}/actions'], function () {
        Route::get('/', [\App\Http\Controllers\Administration\ModeratorGameActionsController::class, 'allForModerator']);
    });
});

Route::group(['prefix' => '/admin', 'middleware' => 'admin-access'], function () {
    Route::group(['prefix' => '/{id}'], function () {
        Route::get('/', [\App\Http\Controllers\Administration\AdministratorController::class, 'getData']);

        Route::group(['prefix' => '/moderators'], function () {
            Route::post('/', [\App\Http\Controllers\Administration\AdministratorController::class, 'createModerator']);
            Route::delete('/', [\App\Http\Controllers\Administration\AdministratorController::class, 'removeModerator']);
        });

        Route::group(['prefix' => '/settings'], function () {
            Route::post('/', [\App\Http\Controllers\Administration\AdministratorSettingsController::class, 'createOrUpdate']);
            Route::get('/', [\App\Http\Controllers\Administration\AdministratorSettingsController::class, 'getByAdmin']);
        });
    });
});

Route::group(['prefix' => '/financial-events'], function () {
    Route::get('/company/{companyId}/total-amount', [\App\Http\Controllers\FinancialEventsController::class, 'getAmountForCompany']);
    Route::get('/admin/{adminId}/total-amount', [\App\Http\Controllers\FinancialEventsController::class, 'getAmountForAdmin']);
});

Route::group(['prefix' => '/payouts'], function () {
    Route::get('/user/{userId}', [\App\Http\Controllers\PayoutsController::class, 'getByUserId']);
    Route::post('/', [\App\Http\Controllers\PayoutsController::class, 'create']);
    Route::post('/{id}/approve', [\App\Http\Controllers\PayoutsController::class, 'approvePayout']);

    Route::get('/', [\App\Http\Controllers\PayoutsController::class, 'all']);
    Route::get('/{id}', [\App\Http\Controllers\PayoutsController::class, 'get']);
});

Route::group(['prefix' => '/settings'], function () {
    Route::get('/{adminId}', [\App\Http\Controllers\Administration\AdministratorSettingsController::class, 'general']);
});

Route::group(['prefix' => '/ads'], function () {
    Route::post('/{adminId}/subscribe/{email}', [\App\Http\Controllers\Ads\AdsSubscribersController::class, 'subscribe']);

    Route::get('/subscribers', [\App\Http\Controllers\Ads\AdsSubscribersController::class, 'index'])->middleware('admin-access');
});

