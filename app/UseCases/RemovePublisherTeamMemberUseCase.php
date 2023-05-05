<?php

namespace App\UseCases;

use App\Events\GameDownloadedEvent;
use App\Events\GameRemovedEvent;
use App\Models\Company;
use App\Models\Game;
use App\Models\Role;
use App\Repositories\CompanyRepository;
use App\Repositories\CustomerGameRepository;
use App\Repositories\GameRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;

class RemovePublisherTeamMemberUseCase
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private UserRepository $userRepository,
        private RoleRepository $roleRepository,
    ) {
    }

    public function handle(int $companyId, array $memberSearchCriteria): Company
    {
        $user = $this->userRepository->findBy([
            'email' => $memberSearchCriteria['email'],
        ]);
        //$user->role_id = $this->roleRepository->findByName(Role::ROLE_CUSTOMER)->id;
        //$user->save();

        $teamMembers = $this->companyRepository->get($companyId)->team_members;

        for ($i = 0; $i < count($teamMembers); $i++) {
            if ($teamMembers[$i]['id'] === $user->id) {
                unset($teamMembers[$i]);
            }
        }

        return $this->companyRepository->update($companyId, [
            'team_members' => array_filter($teamMembers),
        ]);
    }
}
