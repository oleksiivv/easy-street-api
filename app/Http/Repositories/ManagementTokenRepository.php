<?php

namespace App\Http\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ManagementTokenRepository
{
    public function storeUser(User $user): string
    {
        $key = 'current_user';

        $data = [
            'user' => $user,
            'role' => $user->role->name,
        ];

        Cache::put($key, $data, now()->addMinutes(15));

        return $key;
    }

    public function removeUser(): void
    {
        Cache::delete('current_user');
    }

    public function get(string $key): ?array
    {
        return Cache::get('current_user');
    }
}
