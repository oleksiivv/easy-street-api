<?php

namespace App\Repositories;

use App\DTO\GameDTO;
use App\Models\Game;
use Illuminate\Support\Collection;

class GameRepository
{
    public function get(int $id): Game
    {
        return Game::findOrFail($id);
    }

    public function list(array $filter = [], string $sort = 'id', string $direction = Game::GAME_SORT_DIRECTION_ASC): Collection
    {
        $games = Game::where($filter)->orderBy($sort, $direction)->get();

        $result = collect([]);
        $result['data'] = $games;
        $result['pagination'] = [];

        return $result;
    }

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
