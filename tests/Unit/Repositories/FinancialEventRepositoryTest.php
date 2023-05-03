<?php

namespace Tests\Unit\Repositories;

use App\Models\FinancialEvent;
use App\Models\Payout;
use Illuminate\Support\Collection;

class FinancialEventRepositoryTest
{
    public function create(array $data): FinancialEvent
    {
        return FinancialEvent::create($data);
    }

    public function getByCompanyId(int $companyId): Collection
    {
        return FinancialEvent::where(['company_id' => $companyId])->get();
    }

    public function getByAdminId(int $adminId): Collection
    {
        return FinancialEvent::where(['admin_id' => $adminId])->get();
    }

    public function getTotalAmountByCompanyId(int $companyId): int|float
    {
        return array_sum(FinancialEvent::where(['company_id' => $companyId])->pluck('amount')->toArray());
    }

    public function getTotalAmountByAdminId(int $adminId): int|float
    {
        return array_sum(FinancialEvent::where(['admin_id' => $adminId])->pluck('amount')->toArray());
    }
}
