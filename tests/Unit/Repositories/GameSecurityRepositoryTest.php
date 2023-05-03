<?php

namespace Tests\Unit\Repositories;

use App\DTO\GameSecurityDTO;
use App\Models\GameSecurity;
use Throwable;

class GameSecurityRepositoryTest
{
    public function create(GameSecurityDTO $data, int $gameId): GameSecurity
    {
        $data->game_id = $gameId;

        return GameSecurity::create(array_filter($data->toArray()));
    }

    public function update(?int $id, GameSecurityDTO $data): GameSecurity
    {
        try {
            $gameSecurity = GameSecurity::find($id);
            $gameSecurity->update(array_filter($data->toArray()));

            $gameSecurity->save();
        } catch (Throwable) {
            $gameSecurity = GameSecurity::create(array_filter($data->toArray()));
        }

        return $gameSecurity->refresh();
    }
}
