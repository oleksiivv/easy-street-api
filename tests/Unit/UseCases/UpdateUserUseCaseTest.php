<?php

namespace Tests\Unit\UseCases;

namespace Tests\Unit\UseCases;

use App\Models\User;
use App\Repositories\UserRepository;
use App\UseCases\UpdateUserUseCase;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UpdateUserUseCaseTest extends TestCase
{
    public function testHandle()
    {
        // Create a mock for the UserRepository
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('update')
            ->willReturn(new User(['id' => 1]));

        // Create an instance of the UpdateUserUseCase
        $useCase = new UpdateUserUseCase($userRepository);

        // Create sample data for updating the user
        $id = 1;
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        // Call the handle method
        $result = $useCase->handle($id, $data);

        // Assert the result
        $this->assertInstanceOf(User::class, $result);
    }

    public function testHandleThrowsUnauthorizedHttpException()
    {
        // Create a mock for the UserRepository
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('update')
            ->willThrowException(new \Exception());

        // Create an instance of the UpdateUserUseCase
        $useCase = new UpdateUserUseCase($userRepository);

        // Create sample data for updating the user
        $id = 1;
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        // Assert that an UnauthorizedHttpException is thrown
        $this->expectException(HttpException::class);

        // Call the handle method
        $useCase->handle($id, $data);
    }
}
