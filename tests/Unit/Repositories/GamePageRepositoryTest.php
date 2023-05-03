<?php

namespace Tests\Unit\Repositories;

use App\DTO\GamePageDTO;
use App\Models\GamePage;
use Throwable;

class GamePageRepositoryTest
{
    public function create(GamePageDTO $data, int $gameId): GamePage
    {
        $data->game_id = $gameId;

        return GamePage::create($data->toArray());
    }

    public function update(?int $id, GamePageDTO $data): GamePage
    {
        try {
            $gamePage = GamePage::find($id);
            $gamePage->update(array_filter($data->toArray()));

            $gamePage->save();
        } catch (Throwable) {
            $gamePage = GamePage::create($data->toArray());
        }
        return $gamePage;
    }

    public function updateByArray(?int $id, array $data): GamePage
    {
        try {
            $gamePage = GamePage::find($id);
            $gamePage->update(array_filter($data, function ($item) {
                return $item !== null;
            }));

            $gamePage->save();
        } catch (Throwable) {
            $gamePage = GamePage::create($data);
        }
        return $gamePage;
    }
}
