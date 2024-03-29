<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetCompaniesRequest;
use App\Http\Requests\GetGamesRequest;
use App\Http\Requests\GetPublishersRequest;
use App\Models\Subscription;
use App\Repositories\CompanyRepository;
use App\Repositories\CustomerGameRepository;
use App\Repositories\DownloadsRepository;
use App\Repositories\GameRepository;
use App\Repositories\LikesRepository;
use App\Repositories\UserSubscriptionsRepository;
use App\UseCases\AddGameToFavouritesUseCase;
use App\UseCases\DownloadGameUseCase;
use App\UseCases\RateGameUseCase;
use App\UseCases\RemoveGameFromFavouritesUseCase;
use App\UseCases\RemoveGameUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerPublishersController extends Controller
{
    public function __construct(
        private GameRepository $gameRepository,
        private CustomerGameRepository $customerGameRepository,
        private DownloadGameUseCase $downloadGameUseCase,
        private RemoveGameUseCase $removeGameUseCase,
        private AddGameToFavouritesUseCase $addGameToFavouritesUseCase,
        private RemoveGameFromFavouritesUseCase $removeGameFromFavouritesUseCase,
        private RateGameUseCase $rateGameUseCase,
        private DownloadsRepository $downloadsRepository,
        private LikesRepository $likesRepository,
        private CompanyRepository $companyRepository,
        private UserSubscriptionsRepository $userSubscriptionsRepository,
    ) {
    }

    public function index(GetCompaniesRequest $getCompaniesRequest): Response
    {
        $data = $this->companyRepository->list(
            $getCompaniesRequest->filters ?? [],
            $getCompaniesRequest->sort ?? 'es_index',
            $getCompaniesRequest->sort_direction ?? 'asc',
        );

        return new Response($data);
    }

    public function stats(int $id): Response
    {
        $company = $this->companyRepository->get($id);

        $likes = $this->likesRepository->getMiddleForCompany($company->id);
        $likesTotal = $this->likesRepository->getCountForCompany($company->id);
        $downloads = $this->downloadsRepository->getCountForCompany($company->id);

        return new Response([
            'downloads' => $downloads,
            'likes' => $likes,
            'likes_total' => $likesTotal,
            'likes_percentage' => $downloads !== 0 ? round($likes / $downloads * 100) : ($likes>0?100:0),
            'subscriptions' => $this->userSubscriptionsRepository->getCount($company->id),
            'company' => $company->toArray(),
        ]);
    }

    public function subscribeOrUnsubscribe(int $companyId, Request $request)
    {
        $this->userSubscriptionsRepository->createOrDelete([
            'user_id' => $request->user_id,
            'publisher_id' => $companyId,
        ]);

        return response()->noContent();
    }

    public function subscriptions(int $userId)
    {
        $games = [];

        $recomendations = $this->gameRepository->list([
            'status' => "[\"active\"]",
        ]);

        $recomendationsFrom = rand(min($recomendations->count() - 5, 0), $recomendations->count()-2);

        $recomendations = $recomendations->slice($recomendationsFrom, min($recomendations->count(), $recomendationsFrom + rand(5, 15)));

        collect($this->userSubscriptionsRepository->list([
            'user_id' => $userId,
        ]))->transform(function ($item) use (&$games, $recomendations) {
            $item['publisher']['games'] = $item['publisher']['games']?->map(function ($game) use ($recomendations) {
                return in_array($game, $recomendations->toArray()['data'] ?? []) || empty($recomendations->toArray()['data'])
                    ? $game->load('gamePage')
                    : null;
            });

            $games = array_merge($games, array_filter($item['publisher']['games']?->toArray() ?? []));
        });

        $games = array_slice(array_merge($games, $recomendations->toArray()['data'] ?? []), 0, 9);

        return new Response([
            'subscriptions' => $this->userSubscriptionsRepository->list([
                'user_id' => $userId,
            ]),
            'games' => $games,
        ]);
    }

    public function recomendations(Request $request)
    {
        $games = [];

        $recomendations = $this->gameRepository->list([
            'status' => "[\"active\"]",
        ]);

        $recomendationsFrom = rand(max($recomendations->count() - 5, 0), $recomendations->count()-2);

        $recomendations = $recomendations->slice($recomendationsFrom, min($recomendations->count(), $recomendationsFrom + rand(5, 15)));

        $games = array_slice(array_merge($games, $recomendations->toArray()['data'] ?? []), 0, 9);

        return new Response([
            'games' => $games,
        ]);
    }

    public function isSubscribed(int $companyId, Request $request)
    {
        return new Response([
            'subscribed' => $this->userSubscriptionsRepository->exists([
                'user_id' => $request->user_id,
                'publisher_id' => $companyId,
            ])
        ]);
    }

    public function sendMessage(): void
    {
        //TODO: add implementation
        //message can be:
        //1. visible - comment
        //2. private message
        //can be edited by customer or admin
    }
}
