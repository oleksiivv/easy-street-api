<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerAccount\CustomerUpdateRequest;
use App\Http\Requests\CustomerAccount\ForgotPasswordRequest;
use App\Http\Requests\CustomerAccount\LoginRequest;
use App\Http\Requests\CustomerAccount\RegisterRequest;
use App\Http\Resources\PublicUserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use App\UseCases\ConfirmEmailUseCase;
use App\UseCases\ConfirmNewPasswordUseCase;
use App\UseCases\ForgotPasswordUseCase;
use App\UseCases\LoginUseCase;
use App\UseCases\RegisterUseCase;
use App\UseCases\UpdateUserUseCase;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function show(int $id): PublicUserResource
    {
        return new PublicUserResource($this->userRepository->get($id));
    }
}
