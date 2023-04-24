<?php

namespace App\Repositories;

use App\Models\CustomerGame;
use App\Models\Game;
use Illuminate\Support\Collection;

class CustomerGameRepository
{
    public function get(int $id): CustomerGame
    {
        return CustomerGame::findOrFail($id);
    }

    public function list(array $filter = [], string $sort = 'id', string $direction = Game::GAME_SORT_DIRECTION_ASC): Collection
    {
        $games = CustomerGame::where($filter)->orderBy($sort, $direction)->get()->load('game', 'game.gamePage');

        $result = collect([]);
        $result['data'] = $games;
        $result['pagination'] = [];

        return $result;
    }

    public function create(array $data): CustomerGame
    {
        return CustomerGame::create(array_filter($data));
    }

    public function update(int $id, array $data): CustomerGame
    {
        $game = CustomerGame::findOrFail($id);

        $game->update(array_filter($data));

        return $game->refresh();
    }

    public function exists(array $data): int
    {
        return CustomerGame::where($data)->exists();
    }

    public function updateOrCreate(array $search, array $data): void
    {
        CustomerGame::updateOrCreate($search, $data);
    }
}
