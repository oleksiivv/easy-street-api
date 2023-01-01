<?php

namespace App\Http\Requests;

use App\DTO\GameCategoryDTO;
use App\DTO\GameDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGameCategoryRequest extends FormRequest
{
    public function rules()
    {
        return [
            'game_category_id' => 'nullable|int',

            'game_category' => 'nullable|array',
            'game_category.name' => 'nullable|string',
            'game_category.description' => 'nullable|string',
        ];
    }

    public function getGameDTO(): GameDTO
    {
        $data = $this->validated();

        $game = new GameDTO([
            'game_category_id' => data_get($data, 'game_category_id'),
        ]);

        if (isset($data['game_category'])) {
            $game->game_category_data = new GameCategoryDTO($data['game_category']);
        }

        return $game;
    }
}
