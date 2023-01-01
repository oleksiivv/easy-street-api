<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetGamesRequest;
use App\Repositories\CustomerGameRepository;
use App\Repositories\GameRepository;
use App\UseCases\AddGameToFavouritesUseCase;
use App\UseCases\DownloadGameUseCase;
use App\UseCases\RemoveGameFromFavouritesUseCase;
use App\UseCases\RemoveGameUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerGameController extends Controller
{
    public function __construct(
        private GameRepository $gameRepository,
        private CustomerGameRepository $customerGameRepository,
        private DownloadGameUseCase $downloadGameUseCase,
        private RemoveGameUseCase $removeGameUseCase,
        private AddGameToFavouritesUseCase $addGameToFavouritesUseCase,
        private RemoveGameFromFavouritesUseCase $removeGameFromFavouritesUseCase,
    ) {
    }

    public function index(GetGamesRequest $getGamesRequest): Response
    {
        $data = $this->gameRepository->list();

        return new Response($data);
    }

    public function getGame(int $id): Response
    {
        $game = $this->gameRepository->get($id);

        return new Response($game);
    }

    public function getDownloaded(Request $request): Response
    {
        $games = $this->customerGameRepository->list([
            'user_id' => $request->header('user_id'),
            'downloaded' => true,
        ]);

        return new Response($games);
    }

    public function getFavourites(Request $request): Response
    {
        $games = $this->customerGameRepository->list([
            'user_id' => $request->header('user_id'),
            'favourite' => true,
        ]);

        return new Response($games);
    }

    public function getAllTimeDownloaded(Request $request): Response
    {
        $games = $this->customerGameRepository->list([
            ['user_id', '=', $request->header('user_id')],
            ['download_datetime', '!=', null],
        ]);

        return new Response($games);
    }

    public function addToFavourites(int $id, Request $request): Response
    {
        $game = $this->addGameToFavouritesUseCase->handle($id, $request->header('user_id'));

        return new Response($game);
    }

    public function removeFromFavourites(int $id, Request $request): Response
    {
        $game = $this->removeGameFromFavouritesUseCase->handle($id, $request->header('user_id'));

        return new Response($game);
    }

    public function download(int $id, Request $request): Response
    {
        $this->downloadGameUseCase->handle($id, $request->header('user_id'));

        return response()->noContent();
    }

    public function remove(int $id, Request $request): Response
    {
        $this->removeGameUseCase->handle($id, $request->header('user_id'));

        return response()->noContent();
    }
}
