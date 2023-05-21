<?php

namespace Tests\Unit\UseCases;

use App\Events\GameDownloadedEvent;
use App\Events\GameRemovedEvent;
use App\Models\Game;
use App\Models\Like;
use App\Repositories\CustomerGameRepository;
use App\Repositories\GameRepository;
use App\Repositories\LikesRepository;
use App\UseCases\RateGameUseCase;
use PHPUnit\Framework\TestCase;

class RateGameUseCaseTest extends TestCase
{
    private RateGameUseCase $useCase;
    private LikesRepository $likesRepository;
    private GameRepository $gameRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock LikesRepository object
        $this->likesRepository = $this->createMock(LikesRepository::class);

        // Create a mock GameRepository object
        $this->gameRepository = $this->createMock(GameRepository::class);

        // Create an instance of the RateGameUseCase with the mock dependencies
        $this->useCase = new RateGameUseCase($this->likesRepository, $this->gameRepository);
    }

    public function testHandle(): void
    {
        $gameId = 1;
        $customerId = 2;
        $rate = 3;

        $this->likesRepository
            ->expects($this->once())
            ->method('createOrDelete')
            ->with(['game_id' => $gameId, 'user_id' => $customerId]);

        $this->gameRepository
            ->expects($this->once())
            ->method('addToESIndex')
            ->with($gameId, 50);

        $this->useCase->handle($gameId, $customerId, $rate);
    }
}
