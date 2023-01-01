<?php

namespace App\UseCases;

use App\Events\GameDownloadedEvent;
use App\Events\GameRemovedEvent;
use App\Models\Game;
use App\Repositories\CustomerGameRepository;
use App\Repositories\GameRepository;

class AddGameToFavouritesUseCase
{
    public function __construct(
        private CustomerGameRepository $customerGameRepository,
        private GameRepository $gameRepository,
    ) {
    }

    public function handle(int $gameId, int $customerId): Game
    {
        $game = $this->gameRepository->get($gameId);

        $this->customerGameRepository->updateOrCreate(
            [
                'game_id' => $gameId,
                'user_id' => $customerId,
            ],
            [
                'favourite' => true,
            ]
        );

        return $game->refresh();
    }
}
