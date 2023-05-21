<?php

namespace Tests\Unit\UseCases;

use App\Events\GameDownloadedEvent;
use App\Events\GameRemovedEvent;
use App\Models\Company;
use App\Models\Game;
use App\Models\Role;
use App\Models\User;
use App\Repositories\CompanyRepository;
use App\Repositories\CustomerGameRepository;
use App\Repositories\GameRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\MailService;
use App\UseCases\AddPublisherTeamMemberUseCase;
use Exception;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Webmozart\Assert\Assert;

class AddPublisherTeamMemberUseCaseTest extends TestCase
{
    private AddPublisherTeamMemberUseCase $useCase;
    private CompanyRepository|MockInterface $companyRepository;
    private UserRepository|MockInterface $userRepository;
    private MailService|MockInterface $mailService;
    private RoleRepository|MockInterface $roleRepository;
    private Company|MockInterface $company;
    private Role|MockInterface $role;
    private User|MockInterface $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyRepository = $this->mock(CompanyRepository::class);
        $this->userRepository = $this->mock(UserRepository::class);
        $this->mailService = $this->mock(MailService::class);
        $this->roleRepository = $this->mock(RoleRepository::class);
        $this->company = $this->mock(Company::class);
        $this->role = $this->mock(Role::class);
        $this->user = $this->mock(User::class);

        $this->useCase = new AddPublisherTeamMemberUseCase(
            $this->companyRepository,
            $this->userRepository,
            $this->mailService,
            $this->roleRepository
        );
    }

    public function testHandle(): void
    {
        $companyId = 123;
        $memberSearchCriteria = [
            'email' => 'test@example.com',
        ];

        // Mock the behavior of the user repository's findBy method
        $this->userRepository
            ->shouldReceive('findBy')
            ->once()
            ->with(['email' => $memberSearchCriteria['email']])
            ->andReturn($this->user);

        // Mock the behavior of the company repository's get method
        $this->companyRepository
            ->shouldReceive('get')
            ->once()
            ->with($companyId)
            ->andReturn($this->company);

        $this->company->expects('getAttribute')->times(2);
        $this->company->expects('setAttribute');

        $this->user->expects('getAttribute')->times(2);

        // Mock the behavior of the company's team_members property
        $teamMembers = [
            [
                'id' => 1,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
            ],
            [
                'id' => 2,
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
            ],
        ];
        $this->company->team_members = $teamMembers;

        // Mock the behavior of the mail service's sendNewTeamMemberInvitation method
        $this->mailService
            ->shouldReceive('sendNewTeamMemberInvitation')
            ->times(0)
            ->with(
                $this->user->email,
                [
                    'name' => $this->user->name,
                    'companyName' => $this->company->name,
                ],
                'Invitation to the team'
            );

        // Mock the behavior of the role repository's findByName method
        $this->roleRepository
            ->shouldReceive('findByName')
            ->times(0)
            ->with(Role::ROLE_PUBLISHER_TEAM_MEMBER)
            ->andReturn($this->role);

        $this->expectException(HttpException::class);

        $result = $this->useCase->handle($companyId, $memberSearchCriteria);
    }

    public function testHandleThrowsHttpExceptionOnException(): void
    {
        $companyId = 123;
        $memberSearchCriteria = [
            'email' => 'test@example.com',
        ];

        // Mock the behavior of the user repository's findBy method to throw an exception
        $this->userRepository
            ->shouldReceive('findBy')
            ->once()
            ->with(['email' => $memberSearchCriteria['email']])
            ->andThrow(new HttpException(422));

        // Expect that an HttpException is thrown
        $this->expectException(HttpException::class);

        // Call the handle method
        $this->useCase->handle($companyId, $memberSearchCriteria);
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
