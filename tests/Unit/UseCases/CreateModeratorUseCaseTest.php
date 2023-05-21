<?php

namespace Tests\Unit\UseCases;

use App\Models\Administrator;
use App\Models\Role;
use App\Models\User;
use App\Repositories\AdministratorRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\MailService;
use App\UseCases\CreateModeratorUseCase;
use PHPUnit\Framework\TestCase;

class CreateModeratorUseCaseTest extends TestCase
{
    private CreateModeratorUseCase $useCase;
    private AdministratorRepository $administratorRepository;
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;
    private MailService $mailService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock objects for dependencies
        $this->administratorRepository = $this->createMock(AdministratorRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->roleRepository = $this->createMock(RoleRepository::class);
        $this->mailService = $this->createMock(MailService::class);

        // Create an instance of the CreateModeratorUseCase with the mock dependencies
        $this->useCase = new CreateModeratorUseCase(
            $this->administratorRepository,
            $this->userRepository,
            $this->roleRepository,
            $this->mailService
        );
    }

    public function testHandleCreatesAdministratorAndSendsInvitation(): void
    {
        $this->markTestIncomplete("Depends on database content");

        $administratorEmail = 'administrator@example.com';
        $moderatorEmail = 'moderator@example.com';
        $administratorId = '123';

        $administratorData = new Administrator();
        // Set properties for $administratorData

        $this->administratorRepository
            ->expects($this->once())
            ->method('createOrUpdate')
            ->with($administratorEmail, $moderatorEmail, $administratorId)
            ->willReturn($administratorData);

        $moderator = new User();
        // Set properties for $moderator

        $this->userRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['email' => $moderatorEmail])
            ->willReturn($moderator);

        $role = new Role();
        $role->id = 1;
        $role->name = Role::ROLE_MODERATOR;

        $this->roleRepository
            ->expects($this->once())
            ->method('findByName')
            ->with(Role::ROLE_MODERATOR)
            ->willReturn($role);

        $moderator->role_id = $role->id;

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($moderator);

        $this->mailService
            ->expects($this->once())
            ->method('sendModeratorInvitation')
            ->with($moderatorEmail, 'EasyStreet support team invitation');

        $result = $this->useCase->handle($administratorEmail, $moderatorEmail, $administratorId);

        $this->assertSame($administratorData, $result);
    }

    // Add more test methods for other scenarios

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->useCase = null;
        $this->administratorRepository = null;
        $this->userRepository = null;
        $this->roleRepository = null;
        $this->mailService = null;
    }
}
