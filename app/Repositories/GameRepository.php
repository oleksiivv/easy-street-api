<?php

namespace App\Repositories;

use App\DTO\GameDTO;
use App\Models\Game;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GameRepository
{
    public const GAME_GENRES_IMAGES = [
        'arcade' => 'https://wallpaper-house.com/data/out/8/wallpaper2you_287091.png'
    ];

    public function get(int $id): Game
    {
        return Game::findOrFail($id)->load(Game::RELATIONS);
    }

    public function search(string $keyword): Collection
    {
        return Game::query()->where('name', 'LIKE', '%'.$keyword.'%')
            ->orWhereHas('gamePage', function ($query) use ($keyword) {
                $query->where('short_description', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('long_description', 'LIKE', '%'.$keyword.'%');
            })
            ->get()
            ->load(Game::RELATIONS);
    }

    public function list(array $filter = [], string $sort = 'es_index', string $direction = Game::GAME_SORT_DIRECTION_ASC): Collection
    {
        $games = Game::where($filter)->orderBy($sort, $direction)->get()->load(Game::RELATIONS);

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

        $game->update(array_filter($data->toArray(), function ($item) {
            return $item !== null;
        }));

        return $game->load(Game::RELATIONS)->refresh();
    }

    public function updateByArray(int $id, array $data): Game
    {
        $game = Game::findOrFail($id);

        $game->update(array_filter($data, function ($item) {
            return $item !== null;
        }));

        return $game->load(Game::RELATIONS)->refresh();
    }

    public function groupByGenres(): Collection
    {
        return Game::groupBy('genre')->select('genre', DB::raw('count(*) as total'))->get();
    }

    public function addToESIndex(int $gameId, int $number): void
    {
        $game = Game::findOrFail($gameId);
        $game->es_index = $game->es_index + $number;
        $game->save();
     }
}
