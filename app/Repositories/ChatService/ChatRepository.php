<?php

namespace App\Repositories\ChatService;

use App\Models\Chat;
use Illuminate\Support\Collection;

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

    public function getByUserId(int $userId): Collection
    {
        return Chat::query()->whereHas('messages', function ($query) use ($userId) {
            $query->where('user_id', '=', $userId);
        })->get()->load('game', 'game.gamePage');
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
