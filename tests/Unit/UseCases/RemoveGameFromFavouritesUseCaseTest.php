<?php

namespace Tests\Unit\UseCases;

use App\Events\GameDownloadedEvent;
use App\Events\GameRemovedEvent;
use App\Models\Game;
use App\Repositories\CustomerGameRepository;
use App\Repositories\GameRepository;

class RemoveGameFromFavouritesUseCaseTest
{
    public function __construct(
        private CustomerGameRepository $customerGameRepository,
        private GameRepository $gameRepository,
    ) {
    }

    public function handle(int $gameId, int $customerId): Game
    {
        $game = $this->gameRepository->get($gameId);

        $this->gameRepository->addToESIndex($gameId, -10);

        $this->customerGameRepository->updateOrCreate(
            [
                'game_id' => $gameId,
                'user_id' => $customerId,
            ],
            [
                'favourite' => false,
            ]
        );

        return $game->refresh();
    }
}
