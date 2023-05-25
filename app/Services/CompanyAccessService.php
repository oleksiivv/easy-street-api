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

        $teamMembers = $company->team_members;

        if (in_array([
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'id' => $user->id,
        ], $teamMembers)) {
            return false;
        }

        return true;
    }
}
