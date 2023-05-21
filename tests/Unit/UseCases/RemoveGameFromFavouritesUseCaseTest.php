<?php

namespace Tests\Unit\UseCases;

use App\Events\GameDownloadedEvent;
use App\Events\GameRemovedEvent;
use App\Models\Game;
use App\Repositories\CustomerGameRepository;
use App\Repositories\GameRepository;
use App\UseCases\RemoveGameFromFavouritesUseCase;
use PHPUnit\Framework\TestCase;

class RemoveGameFromFavouritesUseCaseTest extends TestCase
{
    public function testHandle()
    {
        // Create a mock for the CustomerGameRepository
        $customerGameRepository = $this->createMock(CustomerGameRepository::class);
        $customerGameRepository->expects($this->once())
            ->method('updateOrCreate')
            ->willReturnCallback(function ($attributes, $values) {
                $this->assertSame(['game_id' => 1, 'user_id' => 1], $attributes);
                $this->assertSame(['favourite' => false], $values);
                return true;
            });

        // Create a mock for the GameRepository
        $gameRepository = $this->createMock(GameRepository::class);
        $gameRepository->expects($this->once())
            ->method('get')
            ->willReturn(new Game(['id' => 1]));

        // Create an instance of the RemoveGameFromFavouritesUseCase
        $useCase = new RemoveGameFromFavouritesUseCase($customerGameRepository, $gameRepository);

        // Call the handle method
        $game = $useCase->handle(1, 1);

        // Assert the returned game instance
        $this->assertInstanceOf(Game::class, $game);
        // ... additional assertions if needed
    }
}
