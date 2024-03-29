<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\UpdateGameESIndexRequest;
use App\Http\Requests\Administration\UpdateGameRequest;
use App\Models\Game;
use App\Models\GameAction;
use App\Repositories\GameActionRepository;
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
        private GameActionRepository $gameActionRepository
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

        $message = $request->attached_message ?? "No message attached";

        if ($request->getGameDTO()->approved) {
            $this->mailService->sendGameApproveEmail($company->publisher->email, [
                'gameName' => $game->name,
                'companyName' => $game->publisher->name,
                'gameId' => $gameId,
                'attachedMessage' => $message,
            ], 'Game Status Updates');
        } else {
            $this->mailService->sendGameInformationEmail($company->publisher->email, [
                'gameName' => $game->name,
                'gameId' => $gameId,
                'attachedMessage' => $message,
            ], 'Game Status Updates');
        }

        $this->gameActionRepository->create([
            'game_id' => $gameId,
            'type' => $game->approved ? 'approve' : 'update',
            'fields' => ['status'],
            'user_id' => $request->user_id,
            'performed_by' => GameAction::PERFORMED_BY_MODERATOR,
        ]);

        if ($request->getGameDTO()->approved) {
            if ($game->paidProduct?->new_price >= 0) {
                $game->paidProduct->price = $game->paidProduct->new_price;
                $game->paidProduct->new_price = -1;

                $game->paidProduct->save();
                $game->refresh();
            }
        }

        return new Response($game);
    }

    public function updateGameESIndex(int $gameId, UpdateGameESIndexRequest $request): Response
    {
        $game = $this->gameRepository->updateByArray($gameId, [
            'es_index' => $request->es_index,
        ]);

        $this->gameActionRepository->create([
            'game_id' => $gameId,
            'type' => 'update',
            'fields' => ['es_index'],
            'user_id' => $request->user_id,
            'performed_by' => GameAction::PERFORMED_BY_MODERATOR,
        ]);

        return new Response($game);
    }
}
