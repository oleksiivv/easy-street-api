<?php

namespace Tests\Unit\Repositories;

use App\Models\GameAction;
use Illuminate\Support\Collection;
use Throwable;

class GameActionRepositoryTest
{
    public function create(array $data): GameAction
    {
        return GameAction::create($data);
    }

    public function update(?int $id, array $data): GameAction
    {
        try {
            $gameAction = GameAction::find($id);
            $gameAction->update(array_filter($data));

            $gameAction->save();
        } catch (Throwable) {
            $gameAction = GameAction::create($data);
        }
        return $gameAction;
    }

    public function list(array $criteria): Collection
    {
        return GameAction::where($criteria)->orderBy('id', 'desc')->get()->load('game');
    }

    public function getAllUsersActions(int $companyId): Collection
    {
        return $this->list([
            'user_id' => $companyId,
        ]);
    }

    public function getAllUsersActionsForGame(int $gameId): Collection
    {
        return $this->list([
            'game_id' => $gameId,
        ]);
    }

    public function getAllPublisherActionsForGame(int $gameId): Collection
    {
        return $this->list([
            'performed_by' => GameAction::PERFORMED_BY_COMPANY,
            'game_id' => $gameId,
        ]);
    }

    public function getAllModeratorActions(int $userId): Collection
    {
        return $this->list([
            'user_id' => $userId,
            'performed_by' => GameAction::PERFORMED_BY_MODERATOR,
        ]);
    }

    public function getAllModeratorGameActions(int $userId, int $gameId): Collection
    {
        return $this->list([
            'user_id' => $userId,
            'game_id' => $gameId,
            'performed_by' => GameAction::PERFORMED_BY_MODERATOR,
        ]);
    }

    public function getAllAdminActions(int $userId): Collection
    {
        return $this->list([
            'user_id' => $userId,
            'performed_by' => GameAction::PERFORMED_BY_ADMIN,
        ]);
    }
}
