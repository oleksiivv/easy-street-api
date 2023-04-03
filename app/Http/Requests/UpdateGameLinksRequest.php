<?php

namespace App\Http\Requests;

use App\DTO\GameDTO;
use App\DTO\GameLinksDTO;
use App\DTO\GamePageDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGameLinksRequest extends FormRequest
{
    public function rules()
    {
        return [
            'google_play' => 'nullable|string',
            'aptoide' => 'nullable|string',
            'amazon_app_store' => 'nullable|string',
            'galaxy_app_store' => 'nullable|string',

            'app_store' => 'nullable|string',
            'tweak_box' => 'nullable|string',
            'cydia' => 'nullable|string',

            'microsoft_store' => 'nullable|string',
            'steam' => 'nullable|string',
            'epic_games_store' => 'nullable|string',
        ];
    }

    public function getGameDTO(): GameDTO
    {
        $data = $this->validated();

        $links = new GameLinksDTO(['links' => $data]);

        return new GameDTO([
            'links' => $links,
        ]);
    }
}
