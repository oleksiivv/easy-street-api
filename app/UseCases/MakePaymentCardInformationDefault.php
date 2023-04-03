<?php

namespace App\UseCases;

use App\Repositories\UserPaymentCardDataRepository;
use Illuminate\Support\Collection;

class MakePaymentCardInformationDefault
{
    public function __construct(private UserPaymentCardDataRepository $userPaymentCardDataRepository)
    {
    }

    public function handle(int $userId, int $defaultPaymentCardId): Collection
    {
        $this->userPaymentCardDataRepository->updateBy([
            'user_id' => $userId
        ], [
            'is_default' => false,
        ]);

        $this->userPaymentCardDataRepository->update($defaultPaymentCardId, [
            'is_default' => true,
        ]);

        return $this->userPaymentCardDataRepository->findBy([
            'user_id' => $userId,
        ]);
    }
}
