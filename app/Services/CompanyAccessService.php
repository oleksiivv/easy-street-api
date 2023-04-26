<?php

namespace App\Services;

use App\Repositories\CompanyRepository;
use App\Repositories\UserRepository;

class CompanyAccessService
{
    public function __construct(private CompanyRepository $companyRepository, private UserRepository $userRepository)
    {
    }

    public function noAccess(int $userId, int $companyId): bool
    {
        $company = $this->companyRepository->get($companyId);

        if ($company->publisher_id === $userId) {
            return false;
        }

        $user = $this->userRepository->get($userId);

        $moderators = $company->moderators;

        if (in_array($user->email, $moderators)) {
            return false;
        }

        return true;
    }
}
