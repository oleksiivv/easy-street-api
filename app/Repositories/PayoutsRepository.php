<?php

namespace App\Repositories;

use App\Models\Payout;
use Illuminate\Support\Collection;

class PayoutsRepository
{
    public function create(array $data): Payout
    {
        return Payout::create($data);
    }

    public function getByUserId(int $userId): Collection
    {
        return Payout::where(['user_id' => $userId])->get()->load('user');
    }

    public function get(int $id): Payout
    {
        return Payout::findOrFail($id)->load('user', 'user.userPaymentCard');
    }

    public function list(array $filter = []): Collection
    {
        return Payout::where($filter)->get()->load('user', 'user.userPaymentCard');
    }

    public function update(int $id, array $data): Payout
    {
        $payout = Payout::findOrFail($id);

        $payout->update($data);

        return $payout->refresh();
    }
}
