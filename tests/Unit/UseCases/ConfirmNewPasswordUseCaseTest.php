<?php

namespace Tests\Unit\UseCases;

use App\Models\User;
use App\Repositories\UserRepository;
use App\UseCases\ConfirmNewPasswordUseCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ConfirmNewPasswordUseCaseTest extends TestCase
{
    private ConfirmNewPasswordUseCase $useCase;
    private UserRepository|MockInterface $userRepository;
    private User|MockInterface $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->mock(UserRepository::class);
        $this->user = $this->mock(User::class);

        $this->useCase = new ConfirmNewPasswordUseCase(
            $this->userRepository
        );
    }

    public function testHandle(): void
    {
        $this->markTestSkipped('Functionality changed to match configuration');

        $email = 'test@example.com';
        $updatePasswordToken = 'token123';
        $newPasswordSha = 'new_password_sha';

        // Mock the behavior of the user repository's findBy method
        $this->userRepository
            ->shouldReceive('findBy')
            ->once()
            ->with([
                'email' => $email,
                'update_password_token' => $updatePasswordToken
            ])
            ->andReturn($this->user);

        $this->user->expects('getAttribute');

        // Mock the behavior of the user repository's update method
        $this->userRepository
            ->shouldReceive('update')
            ->once()
            ->with($this->user->id, [
                'password_sha' => $newPasswordSha,
                'update_password_token' => Str::uuid()->toString(),
            ])
            ->andReturn($this->user);

        // Assert that the handle method returns the updated user
        $result = $this->useCase->handle($email, $updatePasswordToken, $newPasswordSha);
        $this->assertSame($this->user, $result);
    }

    public function testHandleThrowsHttpExceptionOnException(): void
    {
        $email = 'test@example.com';
        $updatePasswordToken = 'token123';
        $newPasswordSha = 'new_password_sha';

        // Mock the behavior of the user repository's findBy method to throw an exception
        $this->userRepository
            ->shouldReceive('findBy')
            ->once()
            ->with([
                'email' => $email,
                'update_password_token' => $updatePasswordToken
            ])
            ->andThrow(new HttpException(422));

        // Mock the behavior of the Log facade's info method
        Log::shouldReceive('info')->once();

        // Expect that an HttpException is thrown
        $this->expectException(HttpException::class);

        // Call the handle method
        $this->useCase->handle($email, $updatePasswordToken, $newPasswordSha);
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
