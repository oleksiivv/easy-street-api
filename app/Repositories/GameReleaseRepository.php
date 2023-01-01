<?php

namespace App\Repositories;

use App\DTO\GameReleaseDTO;
use App\Models\GameRelease;

class GameReleaseRepository
{
    public function create(GameReleaseDTO $data, int $gameId): GameRelease
    {
        $data->game_id = $gameId;

        return GameRelease::create($data->toArray());
    }

    public function update(?int $id, GameReleaseDTO $data): GameRelease
    {
        return GameRelease::findOrUpdate($id, array_filter($data->toArray()));
    }
}
