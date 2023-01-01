<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository
{
    public function get(int $id): User
    {
        return User::findOrFail($id);
    }

    public function findBy(array $data): User
    {
        return User::where($data)->firstOrFail();
    }

    public function list(array $filter = [], string $sort = 'id', string $direction = User::USER_SORT_DIRECTION_ASC): Collection
    {
        $games = User::where($filter)->orderBy($sort, $direction)->get();

        $result = collect([]);
        $result['data'] = $games;
        $result['pagination'] = [];

        return $result;
    }

    public function create(array $data): User
    {
        return User::create(array_filter($data));
    }

    public function update(int $id, array $data): User
    {
        $game = User::findOrFail($id);

        $game->update(array_filter($data));

        return $game->refresh();
    }
}
