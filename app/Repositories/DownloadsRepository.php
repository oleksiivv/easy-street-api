<?php

namespace App\Repositories;

use App\Models\Download;
use Throwable;

class DownloadsRepository
{
    public function createIfNotExists(array $data): Download
    {
        try {
            return Download::where($data)->firstOrFail();
        } catch (Throwable) {
            return Download::create($data);
        }
    }

    public function getCountForGame(int $gameId): int
    {
        return Download::where('game_id', $gameId)->count();
    }

    public function exists(int $gameId, int $userId): int
    {
        return Download::where([
            'game_id' => $gameId,
            'user_id' => $userId,
        ])->exists();
    }

    public function getCountForCompany(int $companyId): int
    {
        return Download::query()->whereHas('game', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->count();
    }
}
