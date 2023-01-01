<?php

namespace App\Http\Requests;

use App\DTO\GameDTO;
use App\DTO\GameReleaseDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGameReleaseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'version' => 'required|string',
            'android_file_url' => 'required|string',
            'ios_file_url' => 'required|string',
            'windows_file_url' => 'required|string',
            'mac_file_url' => 'required|string',
            'linux_file_url' => 'required|string',
            'release_date' => 'required|string',
        ];
    }

    public function getGameDTO(): GameDTO
    {
        $data = $this->validated();

        $gameRelease = new GameReleaseDTO([$data]);

        return new GameDTO([
            'game_release_data' => $gameRelease,
        ]);
    }
}
