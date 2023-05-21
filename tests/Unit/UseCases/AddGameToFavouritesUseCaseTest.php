<?php

namespace Tests\Unit\UseCases;

use App\Events\GameDownloadedEvent;
use App\Events\GameRemovedEvent;
use App\Models\Game;
use App\Repositories\CustomerGameRepository;
use App\Repositories\GameRepository;
use App\UseCases\AddGameToFavouritesUseCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class AddGameToFavouritesUseCaseTest extends TestCase
{
    private AddGameToFavouritesUseCase $useCase;
    private CustomerGameRepository|MockInterface $customerGameRepository;
    private GameRepository|MockInterface $gameRepository;
    private Game|MockInterface $game;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerGameRepository = $this->mock(CustomerGameRepository::class);
        $this->gameRepository = $this->mock(GameRepository::class);
        $this->game = $this->mock(Game::class);

        $this->useCase = new AddGameToFavouritesUseCase(
            $this->customerGameRepository,
            $this->gameRepository
        );
    }

    public function testHandle(): void
    {
        $gameId = 123;
        $customerId = 456;

        // Mock the behavior of the game repository
        $this->gameRepository
            ->shouldReceive('get')
            ->once()
            ->with($gameId)
            ->andReturn($this->game);

        // Mock the behavior of the game repository's addToESIndex method
        $this->gameRepository
            ->shouldReceive('addToESIndex')
            ->once()
            ->with($gameId, 50);

        // Mock the behavior of the customer game repository's updateOrCreate method
        $this->customerGameRepository
            ->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                [
                    'game_id' => $gameId,
                    'user_id' => $customerId,
                ],
                [
                    'favourite' => true,
                ]
            );

        // Mock the behavior of the game's refresh method
        $this->game
            ->shouldReceive('refresh')
            ->once()
            ->andReturn($this->game);

        // Assert that the handle method returns the refreshed game
        $result = $this->useCase->handle($gameId, $customerId);
        $this->assertSame($this->game, $result);
    }

    private function mock(string $className): MockInterface
    {
        return Mockery::mock($className);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
