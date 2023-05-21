<?php

namespace Tests\Unit\UseCases;

use App\Events\GameDownloadedEvent;
use App\Models\Administrator;
use App\Models\Company;
use App\Models\FinancialEvent;
use App\Models\Game;
use App\Models\Role;
use App\Models\UserPaymentCard;
use App\Repositories\CustomerGameRepository;
use App\Repositories\DownloadsRepository;
use App\Repositories\FinancialEventRepository;
use App\Repositories\GameRepository;
use App\Repositories\UserPaymentCardDataRepository;
use App\Services\PaymentService;
use App\UseCases\DownloadGameUseCase;
use DateTime;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\MockObject\IncompatibleReturnValueException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class DownloadGameUseCaseTest extends TestCase
{
    private DownloadGameUseCase $useCase;
    private CustomerGameRepository $customerGameRepository;
    private GameRepository $gameRepository;
    private DownloadsRepository $downloadsRepository;
    private PaymentService $paymentService;
    private UserPaymentCardDataRepository $userPaymentCardDataRepository;
    private FinancialEventRepository $financialEventRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock objects for dependencies
        $this->customerGameRepository = $this->createMock(CustomerGameRepository::class);
        $this->gameRepository = $this->createMock(GameRepository::class);
        $this->downloadsRepository = $this->createMock(DownloadsRepository::class);
        $this->paymentService = $this->createMock(PaymentService::class);
        $this->userPaymentCardDataRepository = $this->createMock(UserPaymentCardDataRepository::class);
        $this->financialEventRepository = $this->createMock(FinancialEventRepository::class);

        // Create an instance of the DownloadGameUseCase with the mock dependencies
        $this->useCase = new DownloadGameUseCase(
            $this->customerGameRepository,
            $this->gameRepository,
            $this->downloadsRepository,
            $this->paymentService,
            $this->userPaymentCardDataRepository,
            $this->financialEventRepository
        );
    }

    public function testHandleDownloadsGameWithoutPayment(): void
    {
        $this->markTestIncomplete("Depends on database content");

        $gameId = 1;
        $customerId = 123;
        $os = 'windows';
        $paymentData = null;

        $game = new Game();
        // Set properties for $game

        $this->gameRepository
            ->expects($this->once())
            ->method('get')
            ->with($gameId)
            ->willReturn($game);

        $this->customerGameRepository
            ->expects($this->once())
            ->method('exists')
            ->with(['game_id' => $gameId, 'user_id' => $customerId])
            ->willReturn(false);

        $game->es_index = $game->es_index + 30;

        $game->expects($this->once())
            ->method('save');

        $this->customerGameRepository
            ->expects($this->once())
            ->method('updateOrCreate')
            ->with(
                [
                    'game_id' => $gameId,
                    'user_id' => $customerId,
                    'os' => strtok($os, '_'),
                ],
                [
                    'downloaded' => true,
                    'download_datetime' => $this->isInstanceOf(DateTime::class),
                    'version' => $game->gameReleases->last()->version,
                    'os' => strtok($os, '_'),
                ]
            );

        $this->downloadsRepository
            ->expects($this->once())
            ->method('createIfNotExists')
            ->with(['game_id' => $gameId, 'user_id' => $customerId, 'category_id' => $game->gameCategory->id]);

        Event::assertDispatched(GameDownloadedEvent::class);

        $result = $this->useCase->handle($gameId, $customerId, $os, $paymentData);

        $this->assertSame($game->getReleaseByOs($os), $result);
    }

    // Add more test methods for other scenarios

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->useCase = null;
        $this->customerGameRepository = null;
        $this->gameRepository = null;
        $this->downloadsRepository = null;
        $this->paymentService = null;
        $this->userPaymentCardDataRepository = null;
        $this->financialEventRepository = null;
    }
}
