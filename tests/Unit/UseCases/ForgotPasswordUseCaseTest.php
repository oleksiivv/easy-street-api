<?php

namespace Tests\Unit\UseCases;

use App\Http\Repositories\ManagementTokenRepository;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\MailService;
use App\UseCases\ForgotPasswordUseCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Webmozart\Assert\Assert;

class ForgotPasswordUseCaseTest extends TestCase
{
    private ForgotPasswordUseCase $useCase;
    private UserRepository $userRepository;
    private MailService $mailService;
    private ManagementTokenRepository $managementTokenRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock objects for dependencies
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->mailService = $this->createMock(MailService::class);
        $this->managementTokenRepository = $this->createMock(ManagementTokenRepository::class);

        // Create an instance of the ForgotPasswordUseCase with the mock dependencies
        $this->useCase = new ForgotPasswordUseCase($this->userRepository, $this->mailService, $this->managementTokenRepository);
    }

    public function testHandleSendsEmailWithNewPassword(): void
    {
        $email = 'test@example.com';
        $newPassword = 'newpassword';

        // Create a mock User object
        $user = new User();
        $user->id = 1;
        $user->email = $email;
        $user->first_name = 'John';
        $user->password_sha = 'abc123';

        $this->userRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['email' => $email])
            ->willReturn($user);

        $this->managementTokenRepository
            ->expects($this->once())
            ->method('removeUser')
            ->with($user->password_sha . $user->email . $user->id);

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->willReturn($user);

        $this->mailService
            ->expects($this->once())
            ->method('sendEmailRecoverPassword');

        $result = $this->useCase->handle($email, $newPassword);

        $this->assertSame($user, $result);
    }
}
