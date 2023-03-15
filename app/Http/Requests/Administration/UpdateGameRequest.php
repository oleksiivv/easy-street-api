<?php

namespace App\Http\Requests\Administration;

use App\DTO\GameCategoryDTO;
use App\DTO\GameDTO;
use App\DTO\GamePageDTO;
use App\DTO\GameReleaseDTO;
use App\DTO\GameSecurityDTO;
use App\DTO\PaidProductDTO;
use App\Models\Game;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGameRequest extends FormRequest
{
    public function rules()
    {
        return [
            'status' => 'nullable|string|in:' . join('", "', Game::STATUSES_AVAILABLE_FOR_MODERATOR),
            'approve' => 'required|boolean',

            'game_security' => 'nullable|array',
            'game_security.has_ads' => 'nullable|boolean',
            'game_security.ads_providers' => 'nullable|array',
            'game_security.privacy_policy_url' => 'nullable|string',
            'game_security.minimum_age' => 'nullable|int',
            'game_security.sensitive_content' => 'nullable|array',
        ];
    }

    public function getGameDTO(): GameDTO
    {
        $data = $this->validated();

        $paidProduct = data_get($data, 'paid_product') ? new PaidProductDTO(data_get($data, 'paid_product')) : null;
        $gamePage = data_get($data, 'game_page') ? new GamePageDTO(data_get($data, 'game_page')) : null;
        $gameRelease = data_get($data, 'game_release') ? new GameReleaseDTO(data_get($data, 'game_release')) : null;
        $gameSecurity = data_get($data, 'game_security') ? new GameSecurityDTO(data_get($data, 'game_security')) : null;

        $game = new GameDTO([
            'name' => data_get($data, 'name'),
            'genre' => data_get($data, 'genre'),
            'status' => data_get($data, 'status'),
            'tags' => data_get($data, 'tags'),
            'site' => data_get($data, 'site'),
            'game_category_id' => data_get($data, 'game_category_id'),
            'paid_product_data' => $paidProduct,
            'game_page_data' => $gamePage,
            'game_release_data' => $gameRelease,
            'game_security_data' => $gameSecurity,
            'approved' => $data['approve'],
        ]);

        if (isset($data['game_category'])) {
            $game->game_category_data = new GameCategoryDTO($data['game_category']);
        }

        return $game;
    }
}
