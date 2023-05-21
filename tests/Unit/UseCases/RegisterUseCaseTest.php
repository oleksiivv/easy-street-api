<?php

namespace Tests\Unit\UseCases;

namespace Tests\Unit\UseCases;

use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\MailService;
use App\UseCases\LoginUseCase;
use App\UseCases\RegisterUseCase;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class RegisterUseCaseTest extends TestCase
{
    private UserRepository $userRepository;
    private LoginUseCase $loginUseCase;
    private MailService $mailService;
    private RoleRepository $roleRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock objects or use dependency injection to set up the dependencies

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->loginUseCase = $this->createMock(LoginUseCase::class);
        $this->mailService = $this->createMock(MailService::class);
        $this->roleRepository = $this->createMock(RoleRepository::class);
    }

    public function testHandle(): void
    {
        // Set up test data
        $data = [
            'email' => 'test@example.com',
            'password' => 'password',
            // Other required data
        ];

        // Set up expectations for login use case
        $this->loginUseCase
            ->expects($this->once())
            ->method('handle')
            ->with($data)
            ->willReturn(new User()); // Return a User object or use a mock

        // Create an instance of the RegisterUseCase
        $useCase = new RegisterUseCase($this->userRepository, $this->loginUseCase, $this->mailService, $this->roleRepository);

        // Call the handle method and assert the expected behavior
        $user = $useCase->handle($data);
        $this->assertInstanceOf(User::class, $user);
    }
}
