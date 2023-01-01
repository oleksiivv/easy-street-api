<?php

namespace App\UseCases;

use App\Events\GameDownloadedEvent;
use App\Repositories\CustomerGameRepository;
use App\Repositories\GameRepository;

class DownloadGameUseCase
{
    public function __construct(
        private CustomerGameRepository $customerGameRepository,
        private GameRepository $gameRepository,
    ) {
    }

    public function handle(int $gameId, int $customerId): void
    {
        $this->gameRepository->get($gameId);

        broadcast(new GameDownloadedEvent([
            'game_id' => $gameId,
            'user_id' => $customerId,
        ]));

        $this->customerGameRepository->updateOrCreate(
            [
                'game_id' => $gameId,
                'user_id' => $customerId,
            ],
            [
                'downloaded' => true,
                'download_datetime' => now(),
            ]
        );
    }
}
