<?php

namespace App\Http\Controllers;

use App\Repositories\FinancialEventRepository;

class FinancialEventsController extends Controller
{
    public function __construct(private FinancialEventRepository $financialEventRepository)
    {
    }

    public function getAmountForCompany(int $companyId): int|float
    {
        return $this->financialEventRepository->getTotalAmountByCompanyId($companyId);
    }

    public function getAmountForAdmin(int $adminId): int|float
    {
        return $this->financialEventRepository->getTotalAmountByAdminId($adminId);
    }
}
