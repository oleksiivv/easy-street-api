<?php

namespace App\Repositories;

use App\DTO\GameCategoryDTO;
use App\Models\GameCategory;

class GameCategoryRepository
{
    public function createOrUpdate(?int $id, GameCategoryDTO $data): GameCategory
    {
        return GameCategory::createOrUpdate($id, $data->toArray());
    }

    public function update(?int $id, GameCategoryDTO $data): GameCategory
    {
        return GameCategory::findOrUpdate($id, array_filter($data->toArray()));
    }
}
