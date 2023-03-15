<?php

namespace App\Repositories\ChatService;

use App\Models\Message;
use Illuminate\Support\Collection;

class MessageRepository
{
    public function getAllFromChat(int $chatId): Collection
    {
        return Message::with('user')->orderBy('created_at', 'desc')->get();
    }

    public function create(int $chatId, int $userId, string $message): Message
    {
        return Message::create([
            'chat_id' => $chatId,
            'user_id' => $userId,
            'message' => $message,
        ]);
    }
}
