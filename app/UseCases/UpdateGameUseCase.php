<?php

namespace App\UseCases;

use App\DTO\GameDTO;
use App\Models\Game;
use App\Repositories\GameCategoryRepository;
use App\Repositories\GamePageRepository;
use App\Repositories\GameReleaseRepository;
use App\Repositories\GameRepository;
use App\Repositories\GameSecurityRepository;

class UpdateGameUseCase
{
    public function __construct(
        private GameRepository $gameRepository,
        private GameReleaseRepository $gameReleaseRepository,
        private GamePageRepository $gamePageRepository,
        private GameSecurityRepository $gameSecurityRepository,
        private GameCategoryRepository $gameCategoryRepository,
    ) {
    }

    public function handle(int $gameId, GameDTO $data): Game
    {
        $game = $this->gameRepository->update($gameId, $data);

        if (isset($data->game_page_data)) $this->gamePageRepository->update($game->page?->id, $data->game_page_data);

        if (isset($data->game_release_data)) $this->gameReleaseRepository->update($game->release?->id, $data->game_release_data);

        if (isset($data->game_security_data)) $this->gameSecurityRepository->update($game->security?->id, $data->game_security_data);

        if (isset($data->game_category_data)) $this->gameCategoryRepository->update($game->category?->id, $data->game_category_data);

        return $game->refresh();
    }
}
