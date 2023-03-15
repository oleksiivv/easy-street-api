<?php

namespace App\Http\Requests;

use App\DTO\GameDTO;
use App\DTO\GameReleaseDTO;
use App\Repositories\GameRepository;
use App\Repositories\System\FileRepository;
use App\System\OperatingSystem;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGameReleaseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'version' => 'required|string',
            'release_date' => 'required|string',
        ];
    }

    public function getGameDTO(): GameDTO
    {
        $data = $this->validated();

        $gameRelease = new GameReleaseDTO($data);

        return new GameDTO([
            'game_release_data' => $gameRelease,
        ]);
    }
}
