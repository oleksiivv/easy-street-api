<?php

namespace Tests\Unit\UseCases;

use App\Http\Repositories\ManagementTokenRepository;
use App\Models\User;
use App\Repositories\UserRepository;
use App\UseCases\ConfirmEmailUseCase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Webmozart\Assert\Assert;

class ConfirmEmailUseCaseTest extends TestCase
{
    private ConfirmEmailUseCase $useCase;
    private UserRepository|MockInterface $userRepository;
    private ManagementTokenRepository|MockInterface $managementTokenRepository;
    private User|MockInterface $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->mock(UserRepository::class);
        $this->managementTokenRepository = $this->mock(ManagementTokenRepository::class);
        $this->user = $this->mock(User::class);

        $this->useCase = new ConfirmEmailUseCase(
            $this->userRepository,
            $this->managementTokenRepository
        );
    }

    public function testHandle(): void
    {
        $email = 'test@example.com';
        $emailConfirmationToken = 'token123';

        // Mock the behavior of the user repository's findBy method
        $this->userRepository
            ->shouldReceive('findBy')
            ->once()
            ->with(['email_confirmation_token' => $emailConfirmationToken])
            ->andReturn($this->user);

        $this->user->expects('getAttribute');

        $this->expectException(HttpException::class);

        $result = $this->useCase->handle($email, $emailConfirmationToken);
    }

    public function testHandleThrowsHttpExceptionOnException(): void
    {
        $email = 'test@example.com';
        $emailConfirmationToken = 'token123';

        // Mock the behavior of the user repository's findBy method to throw an exception
        $this->userRepository
            ->shouldReceive('findBy')
            ->once()
            ->with(['email_confirmation_token' => $emailConfirmationToken])
            ->andThrow(new HttpException(422));

        // Expect that an HttpException is thrown
        $this->expectException(HttpException::class);

        // Call the handle method
        $this->useCase->handle($email, $emailConfirmationToken);
    }

    private function mock(string $className): MockInterface
    {
        return Mockery::mock($className);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
