<?php

namespace App\UseCases;

use App\Events\GameDownloadedEvent;
use App\Events\GameRemovedEvent;
use App\Models\Company;
use App\Models\Game;
use App\Repositories\CompanyRepository;
use App\Repositories\CustomerGameRepository;
use App\Repositories\GameRepository;
use App\Repositories\UserRepository;

class RemovePublisherTeamMemberUseCase
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function handle(int $companyId, array $memberSearchCriteria): Company
    {
        $user = $this->userRepository->findBy($memberSearchCriteria);

        $teamMembersIds = $this->companyRepository->get($companyId)->team_members;

        for ($i = 0; $i < count($teamMembersIds); $i++) {
            if ($teamMembersIds[$i] === $user->id) {
                unset($teamMembersIds[$i]);
            }
        }

        return $this->companyRepository->update($companyId, [
            'team_members' => array_filter($teamMembersIds),
        ]);
    }
}
