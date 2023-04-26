<?php

namespace App\Repositories;

use App\Models\Subscription;
use Illuminate\Support\Collection;
use Throwable;

class UserSubscriptionsRepository
{
    public function createOrDelete(array $data): void
    {
        try {
            Subscription::where($data)->firstOrFail()->delete();
        } catch (Throwable) {
            Subscription::create($data);
        }
    }

    public function exists(array $data): int
    {
        return Subscription::where($data)->exists();
    }

    public function list(array $data): Collection
    {
        return Subscription::where($data)->get()->load('user', 'publisher', 'publisher.games', 'publisher.games.gamePage');
    }

    public function getCount(int $companyId): int
    {
        return Subscription::where('publisher_id', $companyId)->count();
    }
}
