<?php

namespace Tests\Unit\UseCases;

namespace Tests\Unit\UseCases;

use App\Events\SendChatMessageEvent;
use App\Models\Chat;
use App\Models\Game;
use App\Models\Message;
use App\Repositories\ChatService\ChatRepository;
use App\Repositories\ChatService\MessageRepository;
use App\Repositories\GameRepository;
use App\UseCases\SendMessageUseCase;
use PHPUnit\Framework\TestCase;

class SendMessageUseCaseTest extends TestCase
{
    public function testHandle()
    {
        $this->markTestIncomplete("Depends on server configuration");

        // Create a mock for the MessageRepository
        $messageRepository = $this->createMock(MessageRepository::class);
        $messageRepository->expects($this->once())
            ->method('create')
            ->willReturn(new Message(['id' => 1]));

        // Create a mock for the GameRepository
        $gameRepository = $this->createMock(GameRepository::class);
        $gameRepository->expects($this->once())
            ->method('addToESIndex')
            ->with(2, 2);

        // Create a mock for the ChatRepository
        $chatRepository = $this->createMock(ChatRepository::class);

        // Create an instance of the SendMessageUseCase
        $useCase = new SendMessageUseCase($messageRepository, $gameRepository);

        // Call the handle method
        $useCase->handle(1, 2, 'Test message');
    }
}
