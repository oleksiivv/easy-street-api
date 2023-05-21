<?php

namespace Tests\Unit\UseCases;

use App\Http\Repositories\ManagementTokenRepository;
use App\Models\User;
use App\Repositories\UserRepository;
use App\UseCases\LoginUseCase;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class LoginUseCaseTest extends TestCase
{
    private LoginUseCase $useCase;
    private UserRepository $userRepository;
    private ManagementTokenRepository $managementTokenRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock objects for dependencies
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->managementTokenRepository = $this->createMock(ManagementTokenRepository::class);

        // Create an instance of the LoginUseCase with the mock dependencies
        $this->useCase = new LoginUseCase($this->userRepository, $this->managementTokenRepository);
    }

    public function testHandleWithCorrectCredentials(): void
    {
        $email = 'test@example.com';
        $password = 'password';

        // Create a mock User object
        $user = new User();
        $user->email = $email;
        $user->password_sha = sha1($password);

        $this->userRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['email' => $email, 'password_sha' => sha1($password)])
            ->willReturn($user);

        $this->managementTokenRepository
            ->expects($this->once())
            ->method('storeUser')
            ->with($user);

        $result = $this->useCase->handle(['email' => $email, 'password' => $password]);

        $this->assertSame($user, $result);
    }

    public function testHandleWithIncorrectCredentials(): void
    {
        $email = 'test@example.com';
        $password = 'password';

        $this->userRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['email' => $email, 'password_sha' => sha1($password)]);

        $this->useCase->handle(['email' => $email, 'password' => $password]);
    }
}
