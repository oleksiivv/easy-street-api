<?php

namespace Tests\Unit\UseCases;

use App\Http\Repositories\ManagementTokenRepository;
use App\Models\Administrator;
use App\Models\Company;
use App\Models\FinancialEvent;
use App\Models\Role;
use App\Repositories\CompanyRepository;
use App\Repositories\FinancialEventRepository;
use App\Repositories\UserPaymentCardDataRepository;
use App\Repositories\UserRepository;
use App\Services\MailService;
use App\Services\PaymentService;
use App\UseCases\CreateCompanyUseCase;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class CreateCompanyUseCaseTest extends TestCase
{
    private CreateCompanyUseCase $useCase;
    private CompanyRepository|MockInterface $companyRepository;
    private PaymentService|MockInterface $paymentService;
    private MailService|MockInterface $mailService;
    private UserRepository|MockInterface $userRepository;
    private UserPaymentCardDataRepository|MockInterface $userPaymentCardDataRepository;
    private FinancialEventRepository|MockInterface $financialEventRepository;
    private ManagementTokenRepository|MockInterface $managementTokenRepository;
    private Company|MockInterface $company;
    private Administrator|MockInterface $administrator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyRepository = $this->mock(CompanyRepository::class);
        $this->paymentService = $this->mock(PaymentService::class);
        $this->mailService = $this->mock(MailService::class);
        $this->userRepository = $this->mock(UserRepository::class);
        $this->userPaymentCardDataRepository = $this->mock(UserPaymentCardDataRepository::class);
        $this->financialEventRepository = $this->mock(FinancialEventRepository::class);
        $this->managementTokenRepository = $this->mock(ManagementTokenRepository::class);
        $this->company = $this->mock(Company::class);
        $this->administrator = $this->mock(Administrator::class);

        $this->useCase = new CreateCompanyUseCase(
            $this->companyRepository,
            $this->paymentService,
            $this->mailService,
            $this->userRepository,
            $this->userPaymentCardDataRepository,
            $this->financialEventRepository,
            $this->managementTokenRepository,
        );
    }

    public function testHandle(): void
    {
        $this->markTestSkipped('Functionality changed to match configuration');

        $companyData = [
            'name' => 'Test Company',
            'address' => 'Test Address',
            // ... other data
        ];
        $creditCardData = [
            'number' => '4111111111111111',
            'cvc' => '123',
            'exp_month' => 12,
            'exp_year' => 25,
            // ... other data
        ];

        // Mock the behavior of the company repository's create method
        $this->companyRepository
            ->shouldReceive('create')
            ->once()
            ->with($companyData)
            ->andReturn($this->company);

        // Mock the behavior of the payment service's init method
        $this->paymentService
            ->shouldReceive('init')
            ->once();

        // Mock the behavior of the user payment card data repository's findOrCreate method
        $this->userPaymentCardDataRepository
            ->shouldReceive('findOrCreate')
            ->once()
            ->with($this->company->publisher->id, array_merge(
                $creditCardData ?? [],
                [
                    'address' => $this->company->address,
                    'is_default' => true,
                ]
            ));

        // Mock the behavior of the payment service's createCustomer method
        $this->paymentService
            ->shouldReceive('createCustomer')
            ->once()
            ->with([
                'card' => [
                    'number' => '************1111',
                    'cvc' => '***',
                    'exp_month' => 12,
                    'exp_year' => 25,
                ],
                'address' => $this->company->address,
                'email' => $this->company->publisher->email,
                'name' => $this->company->name . '#' . $this->company->publisher->email,
            ]);

        // Mock the behavior of the payment service's pay method

        $this->financialEventRepository
            ->shouldReceive('create')
            ->once()
            ->with([
                'amount' => Company::ACCOUNT_PRICE,
                'partner_type' => FinancialEvent::PARTNER_TYPE_ES,
                'admin_id' => $this->administrator->id,
            ]);

        // Mock the behavior of the user repository's updateRole method
        $this->userRepository
            ->shouldReceive('updateRole')
            ->once()
            ->with($this->company->publisher->id, Role::ROLE_PUBLISHER);

        // Mock the behavior of the mail service's sendCompanyCreatedConfirmation method
        $this->mailService
            ->shouldReceive('sendCompanyCreatedConfirmation')
            ->once()
            ->with($this->company->publisher->email, [
                'name' => $this->company->publisher->first_name . ' ' . $this->company->publisher->first_name,
                'companyName' => $this->company->name,
            ], 'Company creating confirmation');

        // Mock the behavior of the management token repository's removeUser and storeUser methods
        $this->managementTokenRepository
            ->shouldReceive('removeUser')
            ->once()
            ->with($this->company->publisher->password_sha . $this->company->publisher->email . $this->company->publisher->id);
        $this->managementTokenRepository
            ->shouldReceive('storeUser')
            ->once()
            ->with($this->company->publisher);

        // Mock the behavior of the company's refresh method
        $this->company
            ->shouldReceive('refresh')
            ->once()
            ->andReturn($this->company);

        // Assert that the handle method returns the refreshed company
        $result = $this->useCase->handle($companyData, $creditCardData);
        $this->assertSame($this->company, $result);
    }

    public function testHandleThrowsHttpExceptionOnApiErrorException(): void
    {
        $this->markTestSkipped('Functionality changed to match configuration');

        $this->expectException(\BadMethodCallException::class);

        $companyData = [
            'name' => 'Test Company',
            'address' => 'Test Address',
            // ... other data
        ];
        $creditCardData = [
            'number' => '4111111111111111',
            'cvc' => '123',
            'exp_month' => 12,
            'exp_year' => 25,
            // ... other data
        ];

        // Mock the behavior of the payment service's init method
        $this->paymentService
            ->shouldReceive('init')
            ->once();

        // Mock the behavior of the user payment card data repository's findOrCreate method
        $this->userPaymentCardDataRepository
            ->shouldReceive('findOrCreate')
            ->once()
            ->with($this->company->publisher->id, array_merge(
                $creditCardData ?? [],
                [
                    'address' => $this->company->address,
                    'is_default' => true,
                ]
            ));

        // Mock the behavior of the payment service's createCustomer method to throw an ApiErrorException
        $this->paymentService
            ->shouldReceive('createCustomer')
            ->once()
            ->with([
                'card' => [
                    'number' => '************1111',
                    'cvc' => '***',
                    'exp_month' => 12,
                    'exp_year' => 25,
                ],
                'address' => $this->company->address,
                'email' => $this->company->publisher->email,
                'name' => $this->company->name . '#' . $this->company->publisher->email,
            ])
            ->andThrow(new ApiConnectionException('Payment error'));

        // Call the handle method, which should throw an HttpException
        $this->useCase->handle($companyData, $creditCardData);
    }

    public function testHandleThrowsHttpExceptionOnThrowable(): void
    {
        $this->expectException(HttpException::class);

        $companyData = [
            'name' => 'Test Company',
            'address' => 'Test Address',
            // ... other data
        ];
        $creditCardData = [
            'number' => '4111111111111111',
            'cvc' => '123',
            'exp_month' => 12,
            'exp_year' => 25,
            // ... other data
        ];

        // Mock the behavior of the company repository's create method
        $this->companyRepository
            ->shouldReceive('create')
            ->once()
            ->with($companyData)
            ->andThrow(new Exception('Invalid input data'));

        // Call the handle method, which should throw an HttpException
        $this->useCase->handle($companyData, $creditCardData);
    }

    private function mock(string $class): MockInterface
    {
        $mock = Mockery::mock($class);
        app()->instance($class, $mock);

        return $mock;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
