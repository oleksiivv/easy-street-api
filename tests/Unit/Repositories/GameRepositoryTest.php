<?php

namespace Tests\Unit\Repositories;

use App\DTO\GameDTO;
use App\Models\Game;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GameRepositoryTest
{
    public const GAME_GENRES_IMAGES = [
        'arcade' => 'https://wallpaper-house.com/data/out/8/wallpaper2you_287091.png',
        'adventure' => 'https://wallpaper-house.com/data/out/8/wallpaper2you_287091.png',
        'strategy' => 'https://wallpaper-house.com/data/out/8/wallpaper2you_287091.png',
        'quiz' => 'https://wallpaper-house.com/data/out/8/wallpaper2you_287091.png',
        'hyper-casual' => 'https://wallpaper-house.com/data/out/8/wallpaper2you_287091.png',
        'racing' => 'https://wallpaper-house.com/data/out/8/wallpaper2you_287091.png',
        'victory' => 'https://wallpaper-house.com/data/out/8/wallpaper2you_287091.png',
    ];

    public const GAME_PAGINATION_PER_PAGE = 3;

    public function get(int $id): Game
    {
        return Game::findOrFail($id)->load(Game::RELATIONS);
    }

    public function search(string $keyword): Collection
    {
        return Game::query()->whereIn('status', ['active', 'in_review', 'update_in_review'])
            ->where('name', 'LIKE', '%'.$keyword.'%')
            ->orWhereHas('gamePage', function ($query) use ($keyword) {
                $query->where('short_description', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('long_description', 'LIKE', '%'.$keyword.'%');
            })
            ->get()
            ->load(Game::RELATIONS);
    }

    public function list(array $filter = [], string $sort = 'es_index', string $direction = Game::GAME_SORT_DIRECTION_ASC, ?int $page = null): Collection
    {
        if (isset($filter['status'])) {
            $games = Game::whereIn('status', json_decode($filter['status']));
        }

        if (isset($filter['os']) && $filter['os'] != 'undefined') {
            $filter['os'] = json_decode($filter['os']);
            $games = isset($games) ? $games->with('gameReleases', function ($query) use ($filter) {
                return $query->where(array_map(function ($field) {
                    return [$field, '<>', null];
                }, $filter['os']));
            }) : Game::with('gameReleases', function ($query) use ($filter) {
                return $query->where(array_map(function ($field) {
                    return [$field, '<>', null];
                }, $filter['os']));
            });
        } else {
            $games = $games ?? Game::where($filter);
        }

        $gamesCount = $games?->count() ?? 0;

        if ($sort === 'downloads') {
            $games = $games->withCount('downloads')->orderBy('downloads_count', 'desc');
        } elseif ($sort === 'likes') {
            $games = $games->withCount('likes')->orderBy('likes_count', 'desc');
        } else {
            $games = $games->orderBy($sort, $direction);
        }

        if (!isset($page)) {
            $games = $games->get()->load(Game::RELATIONS);
        } else {
            $games = $games->skip(($page - 1) * self::GAME_PAGINATION_PER_PAGE)
                ->take(self::GAME_PAGINATION_PER_PAGE)
                ->get()->load(Game::RELATIONS);
        }

        $result = collect([]);
        $result['data'] = $games->unique();
        $result['pagination'] = [
            'page' => $page,
            'pages' => ceil($gamesCount / self::GAME_PAGINATION_PER_PAGE),
        ];

        return $result;
    }

    public function getBest(int $companyId): ?Game
    {
        return Game::where([
            'company_id' => $companyId
        ])->orderBy('es_index', 'desc')->first();
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
