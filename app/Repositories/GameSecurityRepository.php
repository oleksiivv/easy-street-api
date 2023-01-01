<?php

namespace App\Repositories;

use App\DTO\GameSecurityDTO;
use App\Models\GameSecurity;

class GameSecurityRepository
{
    public function create(GameSecurityDTO $data, int $gameId): GameSecurity
    {
        $data->game_id = $gameId;

        return GameSecurity::create(array_filter($data->toArray()));
    }

    public function update(?int $id, GameSecurityDTO $data): GameSecurity
    {
        return GameSecurity::findOrUpdate($id, array_filter($data->toArray()));
    }
}
