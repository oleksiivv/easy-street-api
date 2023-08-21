<?php

namespace App\Http\Controllers\Administration;

use App\Http\Requests\Administration\CreateModeratorRequest;
use App\Http\Requests\Administration\RemoveModeratorRequest;
use App\Models\Role;
use App\Models\User;
use App\Repositories\AdministratorRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\UseCases\CreateModeratorUseCase;
use App\UseCases\RemoveModeratorUseCase;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Webmozart\Assert\Assert;

class AdministratorController
{
    public function __construct(
        private CreateModeratorUseCase $createModeratorUseCase,
        private RemoveModeratorUseCase $removeModeratorUseCase,
        private UserRepository $userRepository,
        private RoleRepository $roleRepository,
        private AdministratorRepository $administratorRepository,
    ) {
    }

    public function createModerator(int $administratorId, CreateModeratorRequest $request): Response
    {
        try {
            $administrator = $this->userRepository->findBy([
                'email' => $request->administrator_email,
                'role_id' => $this->roleRepository->findByName(Role::ROLE_ADMIN)->id,
            ]);

            Assert::same($administratorId, $administrator->id);
        } catch (Throwable $e) {
            throw new HttpException(401, $e);
        }

        return new Response($this->createModeratorUseCase->handle($request->administrator_email, $request->moderator_email, $administratorId));
    }

    public function removeModerator(int $administratorId, RemoveModeratorRequest $request): Response
    {
        try {
            $administrator = $this->userRepository->findBy([
                'email' => $request->administrator_email,
                'role_id' => $this->roleRepository->findByName(Role::ROLE_ADMIN)->id,
            ]);

            Assert::same($administratorId, $administrator->id);
        } catch (Throwable $e) {
            throw new HttpException(401, $e);
        }

        return new Response($this->removeModeratorUseCase->handle($request->administrator_email, $request->moderator_email, $administratorId));
    }

    public function getData(int $administratorId): Response
    {
        return new Response($this->administratorRepository->getByAdministratorUserId($administratorId));
    }
}
