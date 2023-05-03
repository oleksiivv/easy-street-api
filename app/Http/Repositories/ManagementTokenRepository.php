<?php

namespace App\Http\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

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
        //session([$key => json_encode($data)]);
        //dd(session($key), $key);
        //Cookie::queue($key, json_encode($data), 60);

        return $key;
    }

    public function removeUser(): void
    {
        //session_destroy();
        Cache::delete('current_user');
    }

    public function get(): ?array
    {
        return Cache::get('current_user');
    }
}
