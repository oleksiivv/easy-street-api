<?php

namespace Tests\Unit\Repositories;

use App\Models\Download;
use App\Models\Like;
use Throwable;

class LikesRepositoryTest
{
    public function createIfNotExists(array $data): Like
    {
        try {
            return Like::where($data)->firstOrFail();
        } catch (Throwable) {
            return Like::create($data);
        }
    }

    public function createOrDelete(array $data): void
    {
        try {
            Like::where($data)->firstOrFail()->delete();
        } catch (Throwable) {
            Like::create($data);
        }
    }

    public function exists(int $gameId, int $userId): int
    {
        return Like::where([
            'game_id' => $gameId,
            'user_id' => $userId,
        ])->exists();
    }

    public function getCountForGame(int $gameId): int
    {
        return Like::where('game_id', $gameId)->count();
    }

    public function getMiddle(int $gameId): int
    {
        return array_sum(Like::where('game_id', $gameId)->pluck('rate')->toArray());
    }

    public function getCountForCompany(int $companyId): int
    {
        return Like::query()->whereHas('game', function ($query) use ($companyId) {
            $query->where('company_id', '=', $companyId);
        })->count();
    }

    public function getMiddleForCompany(int $companyId): int
    {
        return array_sum(Like::query()->whereHas('game', function ($query) use ($companyId) {
            $query->where('company_id', '=', $companyId);
        })->pluck('rate')->toArray());
    }
}
