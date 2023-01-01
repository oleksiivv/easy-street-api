<?php

namespace App\Repositories;

use App\DTO\PaidProductDTO;
use App\Models\GameCategory;

class PaidProductRepository
{
    public function create(PaidProductDTO $data, int $gameId): GameCategory
    {
        $data->game_id = $gameId;

        return GameCategory::create(array_filter($data->toArray()));
    }

    public function update(?int $id, PaidProductDTO $data): GameCategory
    {
        return GameCategory::findOrFail($id, array_filter($data->toArray()));
    }
}
