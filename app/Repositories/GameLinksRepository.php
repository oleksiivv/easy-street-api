<?php

namespace App\Repositories;

use App\DTO\GameLinksDTO;
use App\DTO\GameLinkDTO;
use App\Models\GameLink;
use Throwable;

class GameLinksRepository
{
    public function create(GameLinksDTO $data, int $gameId): GameLink
    {
        $data->game_id = $gameId;

        return GameLink::create($data->toArray());
    }

    public function update(?int $id, GameLinksDTO $data): GameLink
    {
        try {
            $gameLinks = GameLink::find($id);
            $gameLinks->update(array_merge($data->links, [
                'game_id' => $data->game_id,
            ]));

            $gameLinks->save();
        } catch (Throwable) {
            $gameLinks = GameLink::create(array_merge($data->links, [
                'game_id' => $data->game_id,
            ]));
        }
        return $gameLinks;
    }

    public function updateByArray(?int $id, array $data): GameLink
    {
        try {
            $gameLinks = GameLink::find($id);
            $gameLinks->update(array_filter($data));

            $gameLinks->save();
        } catch (Throwable) {
            $gameLinks = GameLink::create($data);
        }
        return $gameLinks;
    }
}
