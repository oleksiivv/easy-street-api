<?php

namespace App\UseCases;

use App\Events\SendChatMessageEvent;
use App\Repositories\ChatService\ChatRepository;
use App\Repositories\ChatService\MessageRepository;

class SendMessageUseCase
{
    public function __construct(private MessageRepository $messageRepository)
    {
    }

    public function handle(int $chatId, int $userId, string $message)
    {
        $message = $this->messageRepository->create($chatId, $userId, $message);

        event(new SendChatMessageEvent([
            'message_id' => $message->id,
            'chat_id' => $chatId,
            'user_id' => $userId,
            'message' => $message,
        ]));
    }
}
