<?php

namespace App\Repositories;

use App\DTO\GameCategoryDTO;
use App\Models\Game;
use App\Models\GameCategory;
use Illuminate\Support\Collection;
use Throwable;
use Webmozart\Assert\Assert;

class GameCategoryRepository
{
    public function createOrUpdate(?int $id, GameCategoryDTO $data): GameCategory
    {
        try {
            Assert::notNull($id);

            $game = GameCategory::findOrFail($id);
            $game->update($data->toArray());

            return $game->refresh();
        } catch (Throwable) {
            return GameCategory::create($data->toArray());
        }
    }

    public function findBy(array $criteria): Collection
    {
        return GameCategory::where($criteria)->get();
    }

    public function all(): Collection
    {
        $categories = GameCategory::withCount('downloads')
            ->orderBy('downloads_count', 'desc')
            ->get()
            ->load('games', 'games.gamePage');

        return $categories->filter(function (GameCategory $category) {
            $games = $category->games;
            $activeGames = $games->filter(function ($game) {
                return ! in_array($game->status, Game::STATUSES_BLOCKED);
            });

            return count($activeGames) > 0;
        });
    }

    public function createIfNotExists(GameCategoryDTO $data): GameCategory
    {
        $game = GameCategory::create($data->toArray());

        return $game->refresh();
    }

    public function update(?int $id, GameCategoryDTO $data): GameCategory
    {
        try {
            $gameCategory = GameCategory::find($id);
            $gameCategory->update(array_filter($data->toArray()));

            $gameCategory->save();
        } catch (Throwable) {
            $gameCategory = GameCategory::create(array_filter($data->toArray()));
        }

        return $gameCategory;
    }
}
