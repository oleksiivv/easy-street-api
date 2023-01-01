<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGameRequest;
use App\Http\Requests\GetGamesRequest;
use App\Http\Requests\UpdateGameCategoryRequest;
use App\Http\Requests\UpdateGamePageRequest;
use App\Http\Requests\UpdateGameReleaseRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Requests\UpdateGameSecurityRequest;
use App\Repositories\GameRepository;
use App\UseCases\CreateGameUseCase;
use App\UseCases\UpdateGameUseCase;
use Illuminate\Http\Response;

class PublisherGameController extends Controller
{
    public function __construct(
        private CreateGameUseCase $createGameUseCase,
        private UpdateGameUseCase $updateGameUseCase,
        private GameRepository $gameRepository,
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

    public function createGame(CreateGameRequest $createGameRequest): Response
    {
        $game = $this->createGameUseCase->handle($createGameRequest->getGameDTO());

        return new Response($game);
    }

    public function updateGame(int $gameId, UpdateGameRequest $updateGameRequest): Response
    {
        $game = $this->updateGameUseCase->handle($gameId, $updateGameRequest->getGameDTO());

        return new Response($game);
    }

    public function updateGameRelease(int $gameId, UpdateGameReleaseRequest $updateGameReleaseRequest): Response
    {
        $game = $this->updateGameUseCase->handle($gameId, $updateGameReleaseRequest->getGameDTO());

        return new Response($game);
    }

    public function updateGamePage(int $gameId, UpdateGamePageRequest $updateGamePageRequest): Response
    {
        $game = $this->updateGameUseCase->handle($gameId, $updateGamePageRequest->getGameDTO());

        return new Response($game);
    }

    public function updateGameSecurity(int $gameId, UpdateGameSecurityRequest $updateGameSecurityRequest): Response
    {
        $game = $this->updateGameUseCase->handle($gameId, $updateGameSecurityRequest->getGameDTO());

        return new Response($game);
    }

    public function updateGameCategory(int $gameId, UpdateGameCategoryRequest $updateGameCategoryRequest): Response
    {
        $game = $this->updateGameUseCase->handle($gameId, $updateGameCategoryRequest->getGameDTO());

        return new Response($game);
    }
}
