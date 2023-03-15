<?php

namespace App\UseCases;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPUnit\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Webmozart\Assert\Assert;

class ConfirmNewPasswordUseCase
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function handle(string $email, string $updatePasswordToken, string $newPasswordSha): User
    {
        try {
            $user = $this->userRepository->findBy([
                'email' => $email,
                'update_password_token' => $updatePasswordToken
            ]);

            $user = $this->userRepository->update($user->id, [
                'password_sha' => $newPasswordSha,
                'update_password_token' => Str::uuid()->toString(),
            ]);
        } catch (Throwable $exception) {
            Log::info($exception);
            throw new HttpException(Response::HTTP_UNAUTHORIZED);
        }

        return $user;
    }
}
