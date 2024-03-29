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

class LoginUseCase
{
    public function __construct(private UserRepository $userRepository, private ManagementTokenRepository $managementTokenRepository)
    {
    }

    public function handle(array $data, bool $checkPassword = true): User
    {
        try {
            $searchParams = [
                'email' => $data['email'],
            ];

            if ($checkPassword) {
                $searchParams['password_sha'] = sha1($data['password']);
            }

            $user = $this->userRepository->findBy($searchParams);

            $this->managementTokenRepository->storeUser($user);

            return $user;
        } catch (Throwable $e) {
            throw $e;
            throw new HttpException(Response::HTTP_UNAUTHORIZED);
        }
    }
}
