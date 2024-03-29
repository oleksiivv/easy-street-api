<?php

namespace App\UseCases;

use App\Http\Repositories\ManagementTokenRepository;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\MailService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use PHPUnit\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Webmozart\Assert\Assert;

class ForgotPasswordUseCase
{
    public function __construct(private UserRepository $userRepository, private MailService $mailService, private ManagementTokenRepository $managementTokenRepository)
    {
    }

    public function handle(string $email, string $newPassword): User
    {
        try {
            $user = $this->userRepository->findBy([
                'email' => $email,
            ]);

            $this->managementTokenRepository->removeUser($user->password_sha . $user->email . $user->id);

            $passwordConfirmationToken = Str::uuid()->toString();

            $user = $this->userRepository->update($user->id, [
                'update_password_token' => $passwordConfirmationToken,
            ]);

            $this->mailService->sendEmailRecoverPassword($user->email, [
                'name' => $user->first_name,
                'email' => $user->email,
                'passwordConfirmationToken' => $passwordConfirmationToken,
                'newPassword' => sha1($newPassword),
            ], 'New password confirmation');
        } catch (Throwable $exception) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED);
        }

        return $user;
    }
}
