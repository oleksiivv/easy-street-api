<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChatService\SendMessageRequest;
use App\UseCases\SendMessageUseCase;

class ChatMessageController extends Controller
{
    public function __construct(
        private SendMessageUseCase $sendMessageUseCase,
    ) {
    }

    public function sendMessage(int $chatId, SendMessageRequest $sendMessageRequest): void
    {
        $this->sendMessageUseCase->handle($chatId, $sendMessageRequest->user_id, $sendMessageRequest->message);
    }
}
