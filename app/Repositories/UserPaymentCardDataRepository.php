<?php

namespace App\Repositories;

use App\Models\UserPaymentCard;
use Illuminate\Support\Collection;
use Throwable;
use Webmozart\Assert\Assert;

class UserPaymentCardDataRepository
{
    public function findOrCreate(?int $userId, array $data): UserPaymentCard
    {
        try {
            Assert::notNull($userId);

            return UserPaymentCard::where([
                'user_id' => $userId,
                'is_default' => true,
            ])->firstOrFail();
        } catch (Throwable) {
            return UserPaymentCard::create(array_merge($data, [
                'user_id' => $userId
            ]));
        }
    }

    public function create(int $userId, array $data): UserPaymentCard
    {
        return UserPaymentCard::create(array_merge($data, [
            'user_id' => $userId
        ]));
    }

    public function update(int $id, array $data): UserPaymentCard
    {
        $payment = UserPaymentCard::findOrFail($id);
        $payment->update($data);

        return $payment->refresh();
    }

    public function updateBy(array $search, array $data): void
    {
        UserPaymentCard::where($search)->update($data);
    }

    public function deleteAll(int $userId): void
    {
        UserPaymentCard::where([
            'user_id' => $userId,
        ])->delete();
    }

    public function delete(int $id): void
    {
        $card = UserPaymentCard::findOrFail($id);

        Assert::false($card->is_default);

        $card->delete();
    }

    public function findBy(array $criteria): Collection
    {
        return UserPaymentCard::where($criteria)->get();
    }
}
