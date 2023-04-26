<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChatService\SendMessageRequest;
use App\UseCases\SendMessageUseCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ChatMessageController extends Controller
{
    public function __construct(
        private SendMessageUseCase $sendMessageUseCase,
    ) {
    }

    public function sendMessage(int $chatId, SendMessageRequest $sendMessageRequest): void
    {
        if (data_get($sendMessageRequest, 'user.id') !== $sendMessageRequest->user_id) {
            throw new HttpException(422);
        }

        $this->sendMessageUseCase->handle($chatId, $sendMessageRequest->user_id, $sendMessageRequest->message);
    }
}
