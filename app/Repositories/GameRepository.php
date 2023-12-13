<?php

namespace App\Repositories;

use App\DTO\GameDTO;
use App\Models\Game;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GameRepository
{
    public const GAME_GENRES_IMAGES = [
        'arcade' => 'https://wallpaper-house.com/data/out/8/wallpaper2you_287091.png',
        'adventure' => 'https://cdn.80.lv/api/upload/post/5173/images/5d2ce77e47b9e/widen_1220x0.jpg',
        'strategy' => 'https://e1.pxfuel.com/desktop-wallpaper/788/257/desktop-wallpaper-1440x900-northgard-strategy-games-coast-treasure-box-sword-for-macbook-pro-15-inch-macbook-air-13-inch-northgard.jpg',
        'quiz' => 'https://wallpapercave.com/wp/wp9440268.jpg',
        'hyper-casual' => 'https://azurgames.com/wp-content/uploads/2022/09/1296h729.jpg',
        'racing' => 'https://traxion.gg/wp-content/uploads/2022/03/Race-Condition-3.png',
        'victory' => 'https://cdn.wallpapersafari.com/47/33/Wb06Ol.jpg',
        'other' => 'https://wallpapercave.com/wp/wp1868042.jpg',
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
