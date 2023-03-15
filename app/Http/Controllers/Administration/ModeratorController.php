<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\UpdateGameRequest;
use App\Repositories\GameRepository;
use App\Services\MailService;
use App\UseCases\UpdateGameUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ModeratorController extends Controller
{
    public function __construct(
        private GameRepository $gameRepository,
        private UpdateGameUseCase $updateGameUseCase,
        private MailService $mailService,
    ) {
    }

    public function index(Request $request): Response
    {
        $games = $this->gameRepository->list(
            $request->filters ?? [],
            $request->sort ?? 'updated_at',
            $request->sort_direction ?? 'desc',
        );

        return new Response($games);
    }

    public function getGame(int $gameId): Response
    {
        $game = $this->gameRepository->get($gameId);

        return new Response($game);
    }

    public function updateGame(int $gameId, UpdateGameRequest $request): Response
    {
        $game = $this->updateGameUseCase->handle($gameId, $request->getGameDTO());
        $company = $game->publisher;

        if ($game->approved) {
            $this->mailService->sendGameApproveEmail($company->publisher->email, [
                'gameName' => $game->name,
                'gameId' => $gameId,
            ], 'Game Status Updates');
        } else {
            $this->mailService->sendGameInformationEmail($company->publisher->email, [
                'gameName' => $game->name,
                'gameId' => $gameId,
            ], 'Game Status Updates');
        }

        return new Response($game);
    }
}
