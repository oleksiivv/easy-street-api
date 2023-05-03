<?php

namespace Tests\Unit\UseCases;

use App\Models\Administrator;
use App\Models\Role;
use App\Repositories\AdministratorRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\MailService;

class CreateModeratorUseCaseTest
{
    public function __construct(
        private AdministratorRepository $administratorRepository,
        private UserRepository $userRepository,
        private RoleRepository $roleRepository,
        private MailService $mailService,
    ) {
    }

    public function handle(string $administratorEmail, string $moderatorEmail, string $administratorId): Administrator
    {
        $administratorData = $this->administratorRepository->createOrUpdate($administratorEmail, $moderatorEmail, $administratorId);

        try {
            $moderator = $this->userRepository->findBy([
                'email' => $moderatorEmail,
            ]);

            $moderator->role_id = $this->roleRepository->findByName(Role::ROLE_MODERATOR)->id;
            $moderator->save();
        } catch (\Throwable) {
            //ignore
        }

        $this->mailService->sendModeratorInvitation($moderatorEmail, 'EasyStreet support team invitation');

        return $administratorData;
    }
}
