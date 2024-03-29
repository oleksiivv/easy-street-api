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

class UpdateGameRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'nullable|string',
            'genre' => 'nullable',
            'status' => 'nullable|string',
            'tags' => 'nullable|array',
            'site' => 'nullable|string',
            'is_game_release_enabled' => 'nullable|boolean',
            'game_category_id' => 'nullable|int',

            'game_page' => 'nullable|array',
            'game_page.short_description' => 'nullable|string',
            'game_page.long_description' => 'nullable|string',
            'game_page.icon_url' => 'nullable|string',
            'game_page.background_image_url' => 'nullable|string',
            'game_page.description_images' => 'nullable|array',

            'game_release' => 'nullable_array',
            'game_release.version' => 'nullable|string',
            'game_release.android_file_url' => 'nullable|string',
            'game_release.ios_file_url' => 'nullable|string',
            'game_release.windows_file_url' => 'nullable|string',
            'game_release.mac_file_url' => 'nullable|string',
            'game_release.linux_file_url' => 'nullable|string',

            'game_security' => 'nullable|array',
            'game_security.has_ads' => 'nullable|boolean',
            'game_security.ads_providers' => 'nullable|array',
            'game_security.privacy_policy_url' => 'nullable|string',
            'game_security.minimum_age' => 'nullable|int',
            'game_security.sensitive_content' => 'nullable|array',

            'game_category' => 'nullable|array',
            'game_category.name' => 'nullable|string',
            'game_category.description' => 'nullable|string',

            'paid_product' => 'nullable|array',
            'paid_product.price' => 'nullable|int',
            'paid_product.currency' => 'nullable|string',
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
            'is_game_release_enabled' => data_get($data, 'is_game_release_enabled'),
            'game_category_id' => data_get($data, 'game_category_id'),
            'paid_product_data' => $paidProduct,
            'game_page_data' => $gamePage,
            'game_release_data' => $gameRelease,
            'game_security_data' => $gameSecurity,
        ]);

        if (isset($data['game_category'])) {
            $game->game_category_data = new GameCategoryDTO($data['game_category']);
        }

        return $game;
    }
}
