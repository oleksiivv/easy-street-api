<?php

namespace App\UseCases;

use App\Events\GameDownloadedEvent;
use App\Events\GameRemovedEvent;
use App\Repositories\CustomerGameRepository;
use App\Repositories\GameRepository;

class RemoveGameUseCase
{
    public function __construct(
        private CustomerGameRepository $customerGameRepository,
        private GameRepository $gameRepository,
    ) {
    }

    public function handle(int $gameId, int $customerId): void
    {
        $this->gameRepository->get($gameId);

        event(new GameRemovedEvent([
            'game_id' => $gameId,
            'user_id' => $customerId,
        ]));

        $this->customerGameRepository->updateOrCreate(
            [
                'game_id' => $gameId,
                'user_id' => $customerId,
            ],
            [
                'downloaded' => false,
            ]
        );
    }
}
