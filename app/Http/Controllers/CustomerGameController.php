<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetGamesRequest;
use App\Models\Game;
use App\Models\User;
use App\Repositories\CustomerGameRepository;
use App\Repositories\DownloadsRepository;
use App\Repositories\GameCategoryRepository;
use App\Repositories\GameReleaseRepository;
use App\Repositories\GameRepository;
use App\Repositories\LikesRepository;
use App\Repositories\UserSubscriptionsRepository;
use App\System\OperatingSystem;
use App\UseCases\AddGameToFavouritesUseCase;
use App\UseCases\DownloadGameUseCase;
use App\UseCases\RateGameUseCase;
use App\UseCases\RemoveGameFromFavouritesUseCase;
use App\UseCases\RemoveGameUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomerGameController extends Controller
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
        private GameReleaseRepository $gameReleaseRepository,
        private UserSubscriptionsRepository $userSubscriptionsRepository,
        private GameCategoryRepository $gameCategoryRepository,
    ) {
    }

    public function index(GetGamesRequest $getGamesRequest): Response
    {
        $filter = $getGamesRequest->filters ?? [];
        $data = $this->gameRepository->list(
            $filter,
            $getGamesRequest->sort ?? 'es_index',
            $getGamesRequest->sort_direction ?? 'desc',
            $getGamesRequest->page ?? null
        );

        return new Response($data);
    }

    public function search(string $keyword): Response
    {
        $data = $this->gameRepository->search($keyword);

        return new Response($data);
    }

    public function getGame(int $id): Response
    {
        $game = $this->gameRepository->get($id);

        return new Response($game);
    }

    public function gameStats(int $id): Response
    {
        $game = $this->gameRepository->get($id);

        $likes = $this->likesRepository->getMiddle($game->id);
        $likesTotal = $this->likesRepository->getCountForGame($game->id);
        $downloads = $this->downloadsRepository->getCountForGame($game->id);

        return new Response([
            'downloads' => $downloads,
            'likes' => $likes,
            'likes_total' => $likesTotal,
            'likes_percentage' => $downloads !== 0 ? round($likes / $downloads * 100) : ($likes>0?100:0),
            'game' => $game->toArray(),
        ]);
    }

    public function gameStatsForUser(int $id, int $userId, Request $request): Response
    {
        if (data_get($request, 'user.id') !== $userId) {
            throw new HttpException(401);
        }

        $game = $this->gameRepository->get($id);

        return new Response([
            'downloaded' => $this->downloadsRepository->exists($game->id, $userId),
            'download_state' => [
                'android' => $this->getGameState($game, $userId, strtok(OperatingSystem::ANDROID, '_')),
                'ios' => $this->getGameState($game, $userId, strtok(OperatingSystem::IOS, '_')),
                'windows' => $this->getGameState($game, $userId, strtok(OperatingSystem::WINDOWS, '_')),
                'linux' => $this->getGameState($game, $userId, strtok(OperatingSystem::OTHER, '_')),
                'mac' => $this->getGameState($game, $userId, strtok(OperatingSystem::MAC, '_')),
            ],
            'liked' => $this->likesRepository->exists($game->id, $userId),
            'favourite' => $this->customerGameRepository->exists([
                'game_id' => $id, 'user_id' => $userId, 'favourite' => true,
            ]),
        ]);
    }

    public function getDownloaded(Request $request): Response
    {
        $games = $this->customerGameRepository->list([
            'user_id' => $request->user_id,
            'downloaded' => true,
        ]);

        return new Response($games);
    }

    public function getFavourites(Request $request): Response
    {
        $games = $this->customerGameRepository->list([
            'user_id' => $request->user_id,
            'favourite' => true,
        ]);

        return new Response($games);
    }

    public function getAllTimeDownloaded(Request $request): Response
    {
        $games = $this->customerGameRepository->list([
            ['user_id', '=', $request->user_id],
            ['download_datetime', '!=', null],
        ]);

        return new Response($games);
    }

    public function addToFavourites(int $id, Request $request): Response
    {
        if (data_get($request, 'user.id') !== $request->user_id) {
            throw new HttpException(401);
        }

        $game = $this->addGameToFavouritesUseCase->handle($id, $request->user_id);

        return new Response($game);
    }

    public function removeFromFavourites(int $id, Request $request): Response
    {
        if (data_get($request, 'user.id') !== $request->user_id) {
            throw new HttpException(401);
        }

        $game = $this->removeGameFromFavouritesUseCase->handle($id, $request->user_id);

        return new Response($game);
    }

    public function rate(int $gameId, Request $request): Response
    {
        if (data_get($request, 'user.id') !== $request->user_id) {
            throw new HttpException(401);
        }

        $this->rateGameUseCase->handle($gameId, $request->user_id, $request->rate);

        return response()->noContent();
    }

    public function download(int $id, Request $request): JsonResponse
    {
        $filepath = $this->downloadGameUseCase->handle($id, $request->user_id, $request->os, $request->payment);

        return response()->json(['filepath' => $filepath]);
    }

    public function getAvailableOSs(int $gameId) : array
    {
        $versions = $this->gameReleaseRepository->getAvailableVersions($gameId);

        $images = $versions['images'] ?? [];
        if (isset($versions['images'])) {
            unset($versions['images']);
        }

        $result = [];

        foreach ($versions as $key=>$value){
            $result[] = [
                'os' => strtok($key, '_'),
                'field' => $key,
                'link' => $value,
                'image' => $images[$key] ?? null,
            ];
        }

        return $result;
    }

    public function remove(int $id, Request $request): Response
    {
        if (data_get($request, 'user.id') !== $request->user_id) {
            throw new HttpException(401);
        }

        $this->removeGameUseCase->handle($id, $request->user_id);

        return response()->noContent();
    }

    private function getGameState(Game $game, int $userId, string $os): string
    {
        $lastVersion = $game->gameReleases?->last()?->version;

        return $this->customerGameRepository->exists([
            'game_id' => $game->id,
            'user_id' => $userId,
            'os' => $os,
            'version' => $lastVersion
        ]) ? 'download' : (
            $this->customerGameRepository->exists([
                'game_id' => $game->id,
                'user_id' => $userId,
                'os' => $os,
            ]) ? 'update' : 'download'
        );
    }

    public function categories(): Response
    {
        return new Response($this->gameCategoryRepository->all());
    }

    public function groupGamesByGenres(): Response
    {
        return new Response($this->gameRepository->groupByGenres()->map(function ($item) {
            $item['image'] = GameRepository::GAME_GENRES_IMAGES[$item['genre']];
            return $item;
        }));
    }
}
