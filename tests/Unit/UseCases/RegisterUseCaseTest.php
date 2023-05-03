<?php

namespace Tests\Unit\UseCases;

use App\Models\Administrator;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\MailService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use PHPUnit\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class RegisterUseCaseTest
{
    public function __construct(
        private UserRepository   $userRepository,
        private LoginUseCaseTest $loginUseCase,
        private MailService      $mailService,
        private RoleRepository   $roleRepository,
    ) {
    }

    public function handle(array $data): User
    {
        try {
            $user = $this->loginUseCase->handle($data);
        } catch (Throwable) {
            try {
                $data['password_sha'] = sha1($data['password']);
                $data['role_id'] = $this->getRole($data['email']);

                $user = $this->userRepository->create($data);
                $this->loginUseCase->handle($data);

                $emailConfirmationToken = Str::uuid()->toString();

                $user = $this->userRepository->update($user->id, [
                    'email_confirmation_token' => $emailConfirmationToken,
                ]);

                $this->mailService->sendEmailConfirmation([$user->email], [
                    'email' => $user->email,
                    'name' => $user->first_name,
                    'emailConfirmationToken' => $emailConfirmationToken,
                ], 'Email Confirmation');
            }
            catch (Throwable $exception) {
                throw new HttpException(422, "Wrong input data. Couldn't register your account. Please, try again.");
            }
        }

        return $user;
    }

    private function getRole(string $email): int
    {
        $roleName = Role::ROLE_CUSTOMER;
        if (Administrator::where('moderators', 'like', "%\"{$email}\"%")->exists()) {
            $roleName = Role::ROLE_MODERATOR;
        }

        if (Company::where('team_members', 'like', "%\"{$email}\"%")->exists()) {
            $roleName = Role::ROLE_PUBLISHER_TEAM_MEMBER;
        }

        return $this->roleRepository->findByName($roleName)->id;
    }
}
