<?php

namespace App\Repositories;

use App\DTO\GamePageDTO;
use App\Models\GamePage;

class GamePageRepository
{
    public function create(GamePageDTO $data, int $gameId): GamePage
    {
        $data->game_id = $gameId;

        return GamePage::create($data->toArray());
    }

    public function update(?int $id, GamePageDTO $data): GamePage
    {
        return GamePage::findOrUpdate($id, array_filter($data->toArray()));
    }
}
