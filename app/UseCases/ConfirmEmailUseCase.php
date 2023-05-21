<?php

namespace App\UseCases;

use App\Http\Repositories\ManagementTokenRepository;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Webmozart\Assert\Assert;

class ConfirmEmailUseCase
{
    public function __construct(private UserRepository $userRepository, private ManagementTokenRepository $managementTokenRepository)
    {
    }

    public function handle(string $email, string $emailConfirmationToken): User
    {
        try {
            $user = $this->userRepository->findBy([
                'email_confirmation_token' => $emailConfirmationToken
            ]);

            Assert::same($user->email, $email);

            $this->managementTokenRepository->removeUser($user->password_sha . $user->email . $user->id);
            $this->managementTokenRepository->storeUser($user);
        } catch (Throwable) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED);
        }

        return $this->userRepository->update($user->id, [
            'email_is_confirmed' => true
        ]);
    }
}
