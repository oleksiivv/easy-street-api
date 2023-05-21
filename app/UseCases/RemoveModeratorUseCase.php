<?php

namespace App\UseCases;

use App\Models\Administrator;
use App\Models\Role;
use App\Repositories\AdministratorRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\MailService;

class RemoveModeratorUseCase
{
    public function __construct(
        private AdministratorRepository $administratorRepository,
        private UserRepository $userRepository,
        private RoleRepository $roleRepository,
    ) {
    }

    public function handle(string $administratorEmail, string $moderatorEmail, string $administratorId): Administrator
    {
        $administratorData = $this->administratorRepository->removeModerator($administratorEmail, $moderatorEmail, $administratorId);

        try {
            $moderator = $this->userRepository->findBy([
                'email' => $moderatorEmail,
            ]);

            $moderator->role_id = $this->roleRepository->findByName(Role::ROLE_CUSTOMER)->id;
            $moderator->save();
        } catch (\Throwable) {
            //ignore
        }

        return $administratorData;
    }
}
