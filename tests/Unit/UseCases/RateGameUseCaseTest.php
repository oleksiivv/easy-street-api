<?php

namespace Tests\Unit\UseCases;

use App\Events\GameDownloadedEvent;
use App\Events\GameRemovedEvent;
use App\Models\Game;
use App\Models\Like;
use App\Repositories\CustomerGameRepository;
use App\Repositories\GameRepository;
use App\Repositories\LikesRepository;

class RateGameUseCaseTest
{
    public function __construct(
        private LikesRepository $likesRepository,
        private GameRepository $gameRepository,
    ) {
    }

    public function handle(int $gameId, int $customerId, int $rate): void
    {
        //TODO: implement rate diapason
        $this->likesRepository->createOrDelete([
            'game_id' => $gameId,
            'user_id' => $customerId,
        ]);

        $this->gameRepository->addToESIndex($gameId, 50);
    }
}
