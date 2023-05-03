<?php

namespace Tests\Unit\UseCases;

use App\Events\SendChatMessageEvent;
use App\Repositories\ChatService\ChatRepository;
use App\Repositories\ChatService\MessageRepository;
use App\Repositories\GameRepository;

class SendMessageUseCaseTest
{
    public function __construct(private MessageRepository $messageRepository, private GameRepository $gameRepository)
    {
    }

    public function handle(int $chatId, int $userId, string $message)
    {
        $message = $this->messageRepository->create($chatId, $userId, $message);

        $this->gameRepository->addToESIndex($message->chat->game->id, 2);

        event(new SendChatMessageEvent([
            'message_id' => $message->id,
            'chat_id' => $chatId,
            'user_id' => $userId,
            'message' => $message,
        ]));
    }
}
