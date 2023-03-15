<?php

namespace App\UseCases;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Webmozart\Assert\Assert;

class UpdateUserUseCase
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function handle(int $id, array $data): User
    {
        try {
            $user = $this->userRepository->update($id, $data);
        } catch (Throwable $exception) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED);
        }

        return $user;
    }
}
