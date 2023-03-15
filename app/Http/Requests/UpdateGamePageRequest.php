<?php

namespace App\Http\Requests;

use App\DTO\GameDTO;
use App\DTO\GamePageDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGamePageRequest extends FormRequest
{
    public function rules()
    {
        return [
            'short_description' => 'required|string',
            'long_description' => 'required|string',
            'icon_url' => 'required|string',
            'background_image_url' => 'required|string',
            'description_images' => 'required|array',
        ];
    }

    public function getGameDTO(): GameDTO
    {
        $data = $this->validated();

        $gamePage = new GamePageDTO($data);

        return new GameDTO([
            'game_page_data' => $gamePage,
        ]);
    }
}
