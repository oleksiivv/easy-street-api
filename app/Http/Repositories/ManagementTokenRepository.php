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
        $key = 'current_user_cookie';

        $data = [
            'user' => $user,
            'role' => $user->role->name,
        ];

        //Cache::put($key, $data, now()->addMinutes(15));
        setcookie($key, json_encode($data));
        //Cookie::queue($key, json_encode($data), 60);

        return $key;
    }

    public function removeUser(): void
    {
        setcookie('current_user_cookie', null);
        //Cache::delete('current_user_cookie');
    }

    public function get(Request $request): ?array
    {
        return json_decode($request->cookie('current_user_cookie'), true);
    }
}
