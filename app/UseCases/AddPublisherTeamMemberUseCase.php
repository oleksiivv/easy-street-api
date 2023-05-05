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
use App\Services\MailService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Webmozart\Assert\Assert;

class AddPublisherTeamMemberUseCase
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private UserRepository $userRepository,
        private MailService $mailService,
        private RoleRepository $roleRepository,
    ) {
    }

    public function handle(int $companyId, array $memberSearchCriteria): Company
    {
        try {
            $user = $this->userRepository->findBy($memberSearchCriteria);

            Assert::eq($this->roleRepository->get($user->role_id)->name, Role::ROLE_CUSTOMER);

            $company = $this->companyRepository->get($companyId);

            $teamMembersIds = $company->team_members;
            $teamMembersIds[] = $user->id;

            $this->mailService->sendNewTeamMemberInvitation($user->email, [
                'name' => $user->name,
                'companyName' => $company->name,
            ], 'Invitation to the team');

            //TODO: fix after diploma
            if ($user->role->name === Role::ROLE_CUSTOMER) {
                $user->role_id = $this->roleRepository->findByName(Role::ROLE_PUBLISHER_TEAM_MEMBER)->id;
            }

            $user->save();

            return $this->companyRepository->update($companyId, [
                'team_members' => collect(array_unique($teamMembersIds))->map(function ($id) {
                    return $this->userRepository->get($id);
                })->toArray(),
            ]);
        } catch (Throwable $exception) {
            throw new HttpException(422, "Wrong input data. Couldn't add team member.");
        }
    }
}
