<?php

namespace Tests\Unit\UseCases;

use App\DTO\GameDTO;
use App\Models\Game;
use App\Repositories\GameCategoryRepository;
use App\Repositories\GameLinksRepository;
use App\Repositories\GamePageRepository;
use App\Repositories\GameReleaseRepository;
use App\Repositories\GameRepository;
use App\Repositories\GameSecurityRepository;
use App\Repositories\PaidProductRepository;

class UpdateGameUseCaseTest
{
    public function __construct(
        private GameRepository $gameRepository,
        private GameReleaseRepository $gameReleaseRepository,
        private GamePageRepository $gamePageRepository,
        private GameSecurityRepository $gameSecurityRepository,
        private GameCategoryRepository $gameCategoryRepository,
        private PaidProductRepository $paidProductRepository,
        private GameLinksRepository $gameLinksRepository,
    ) {
    }

    public function handle(int $gameId, GameDTO $data): Game
    {
        $game = $this->gameRepository->update($gameId, $data);

        if (isset($data->game_page_data)) {
            $data->game_page_data->game_id = $gameId;

            $this->gamePageRepository->update($game->gamePage?->id, $data->game_page_data);
        }

        if (isset($data->game_release_data)) {
            $data->game_release_data->game_id = $gameId;

            $this->gameReleaseRepository->update($data->game_release_data);
        }

        if (isset($data->game_security_data)) {
            $data->game_security_data->game_id = $gameId;

            $this->gameSecurityRepository->update($game->gameSecurity?->id, $data->game_security_data);
        }

        if (isset($data->links)) {
            $data->links->game_id = $gameId;

            $this->gameLinksRepository->update($game->gameLinks?->id, $data->links);
        }

        if (isset($data->game_category_data)) {
            $data->game_category_data->game_id = $gameId;
            $data->game_category_data->company_id = $game->company_id;

            $gameCategory = $this->gameCategoryRepository->createIfNotExists($data->game_category_data);
            $game->game_category_id = $gameCategory->id;
            $game->save();
        }

        if (isset($data->paid_product_data)) {
            $data->paid_product_data->game_id = $gameId;

            $this->paidProductRepository->update($game->paidProduct?->id, $data->paid_product_data);
        }

        return $game->load(Game::RELATIONS)->refresh();
    }
}
