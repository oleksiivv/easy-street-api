<?php

namespace App\Http\Requests;

use App\DTO\GameCategoryDTO;
use App\DTO\GameDTO;
use App\DTO\GamePageDTO;
use App\DTO\GameReleaseDTO;
use App\DTO\GameSecurityDTO;
use App\DTO\PaidProductDTO;
use App\Models\Game;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGameSecurityRequest extends FormRequest
{
    public function rules()
    {
        return [
            'has_ads' => 'required|boolean',
            'ads_providers' => 'required|array',
            'privacy_policy_url' => 'required|string',
            'minimum_age' => 'required|int',
            'sensitive_content' => 'required|array',
        ];
    }

    public function getGameDTO(): GameDTO
    {
        $data = $this->validated();

        $gameSecurity = new GameSecurityDTO($data);

        return new GameDTO([
            'game_security_data' => $gameSecurity,
        ]);
    }
}
