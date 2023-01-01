<?php

namespace App\UseCases;

use App\DTO\GameDTO;
use App\Models\Game;
use App\Repositories\GameCategoryRepository;
use App\Repositories\GamePageRepository;
use App\Repositories\GameReleaseRepository;
use App\Repositories\GameRepository;
use App\Repositories\GameSecurityRepository;
use App\Repositories\PaidProductRepository;

class CreateGameUseCase
{
    public function __construct(
        private GameRepository $gameRepository,
        private GameReleaseRepository $gameReleaseRepository,
        private GamePageRepository $gamePageRepository,
        private GameSecurityRepository $gameSecurityRepository,
        private GameCategoryRepository $gameCategoryRepository,
        private PaidProductRepository $paidProductRepository,
    ) {
    }

    public function handle(GameDTO $data): Game
    {
        $game = $this->gameRepository->create($data);

        if (isset($data->game_page_data)) $this->gamePageRepository->create($data->game_page_data, $game->id);

        if (isset($data->game_release_data)) $this->gameReleaseRepository->create($data->game_release_data, $game->id);

        if (isset($data->game_security_data)) $this->gameSecurityRepository->create($data->game_security_data, $game->id);

        if (isset($data->game_category_data)) $this->gameCategoryRepository->createOrUpdate($data->game_category_id, $data->game_category_data);

        if (isset($data->paid_product_data)) $this->paidProductRepository->create($data->paid_product_data, $game->id);

        return $game->refresh();
    }
}
