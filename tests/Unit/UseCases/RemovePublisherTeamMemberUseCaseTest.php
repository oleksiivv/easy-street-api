<?php

namespace Tests\Unit\UseCases;

use App\Models\Administrator;
use App\Models\Role;
use App\Models\User;
use App\Repositories\AdministratorRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\MailService;
use App\UseCases\RemoveModeratorUseCase;
use PHPUnit\Framework\TestCase;

class RemovePublisherTeamMemberUseCaseTest extends TestCase
{
    public function testHandle()
    {
        // Create a mock for the AdministratorRepository
        $administratorRepository = $this->createMock(AdministratorRepository::class);
        $administratorRepository->expects($this->once())
            ->method('removeModerator')
            ->willReturn(new Administrator(['id' => 1]));

        // Create a mock for the UserRepository
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('findBy')
            ->willReturn(new User(['email' => 'moderator@example.com']));

        // Create a mock for the RoleRepository
        $roleRepository = $this->createMock(RoleRepository::class);
        $roleRepository->expects($this->once())
            ->method('findByName')
            ->willReturn(new Role(['id' => 2]));

        // Create an instance of the RemoveModeratorUseCase
        $useCase = new RemoveModeratorUseCase($administratorRepository, $userRepository, $roleRepository);

        // Call the handle method
        $administrator = $useCase->handle('admin@example.com', 'moderator@example.com', '1');

        // Assert the returned administrator instance
        $this->assertInstanceOf(Administrator::class, $administrator);
        // ... additional assertions if needed
    }
}
