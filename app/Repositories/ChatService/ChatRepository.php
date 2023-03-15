<?php

namespace App\Repositories\ChatService;

use App\Models\Chat;

class ChatRepository
{
    public function getById(int $chatId): Chat
    {
        return Chat::findOrFail($chatId);
    }

    public function getByGameId(int $gameId): ?Chat
    {
        return Chat::where([
            'game_id' => $gameId
        ])->first();
    }

    public function exists(int $chatId): bool
    {
        return Chat::find($chatId) !== null;
    }

    public function create(int $gameId): Chat
    {
        return Chat::create([
            'game_id' => $gameId,
        ]);
    }
}
