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
        $key = $user->password_sha . $user->email . $user->id;

        $data = [
            'user' => $user,
            'role' => $user->role->name,
        ];

        Cache::put($key, $data, now()->addMinutes(60));
        //session([$key => json_encode($data)]);
        //dd(session($key), $key);
        //Cookie::queue($key, json_encode($data), 60);

        return $key;
    }

    public function removeUser(?string $key): void
    {
        //session_destroy();
        Cache::delete($key);
    }

    public function get($key): ?array
    {
        return Cache::get($key);
    }
}
