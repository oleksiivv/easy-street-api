<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ManagementTokenRepository;
use App\Http\Requests\CustomerAccount\CustomerUpdateRequest;
use App\Http\Requests\CustomerAccount\ForgotPasswordRequest;
use App\Http\Requests\CustomerAccount\LoginRequest;
use App\Http\Requests\CustomerAccount\RegisterRequest;
use App\Repositories\UserRepository;
use App\UseCases\ConfirmEmailUseCase;
use App\UseCases\ConfirmNewPasswordUseCase;
use App\UseCases\ForgotPasswordUseCase;
use App\UseCases\LoginUseCase;
use App\UseCases\RegisterUseCase;
use App\UseCases\UpdateUserUseCase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AccountController extends Controller
{
    public function __construct(private UserRepository $userRepository, private ManagementTokenRepository $managementTokenRepository)
    {
    }

    public function login(LoginRequest $loginRequest, LoginUseCase $loginUseCase): Response
    {
        return new Response($loginUseCase->handle($loginRequest->all()));
    }

    public function logout(): Response
    {
        $this->managementTokenRepository->removeUser();

        return response()->noContent();
    }

    public function tryLoginViaCache(): Response
    {
        $data = $this->managementTokenRepository->get('current_user');
        Log::info(json_encode($data, JSON_PRETTY_PRINT));

        if (data_get($data, 'user') === null) {
            throw new UnauthorizedException('Unauthorized', 422);
        }

        return new Response($data['user']);
    }

    public function register(RegisterRequest $registerRequest, RegisterUseCase $registerUseCase): Response
    {
        return new Response($registerUseCase->handle($registerRequest->all()));
    }

    public function confirmEmail(string $email, string $confirmEmailToken, ConfirmEmailUseCase $confirmEmailUseCase): Response
    {
        return new Response($confirmEmailUseCase->handle($email, $confirmEmailToken));
    }

    public function update(int $id, CustomerUpdateRequest $customerUpdateRequest, UpdateUserUseCase $updateUserUseCase): Response
    {
        if (data_get($customerUpdateRequest, 'user.id') !== $id) {
            throw new HttpException(422);
        }

        return new Response($updateUserUseCase->handle($id, $customerUpdateRequest->all()));
    }

    public function forgotPassword(ForgotPasswordRequest $forgotPasswordRequest, ForgotPasswordUseCase $forgotPasswordUseCase): Response
    {
        return new Response($forgotPasswordUseCase->handle($forgotPasswordRequest->email, $forgotPasswordRequest->new_password));
    }

    public function confirmNewPassword(string $email, string $newPasswordSha, string $confirmNewPasswordToken, ConfirmNewPasswordUseCase $confirmNewPasswordUseCase): Response
    {
        return new Response($confirmNewPasswordUseCase->handle($email, $confirmNewPasswordToken, $newPasswordSha));
    }
}
