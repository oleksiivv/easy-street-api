<?php

namespace App\Repositories;

use App\DTO\GameDTO;
use App\Models\Game;

class GameRepository
{
    public function create(GameDTO $data): Game
    {
        return Game::create(array_filter($data->toArray()));
    }

    public function update(int $id, GameDTO $data): Game
    {
        $game = Game::findOrFail($id);

        $game->update(array_filter($data->toArray()));

        return $game->refresh();
    }
}
