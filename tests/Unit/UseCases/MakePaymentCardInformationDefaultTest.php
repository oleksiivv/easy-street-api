<?php

namespace Tests\Unit\UseCases;

use App\Repositories\UserPaymentCardDataRepository;
use App\UseCases\MakePaymentCardInformationDefault;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class MakePaymentCardInformationDefaultTest extends TestCase
{
    private MakePaymentCardInformationDefault $useCase;
    private UserPaymentCardDataRepository $userPaymentCardDataRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock UserPaymentCardDataRepository object
        $this->userPaymentCardDataRepository = $this->createMock(UserPaymentCardDataRepository::class);

        // Create an instance of the MakePaymentCardInformationDefault with the mock dependency
        $this->useCase = new MakePaymentCardInformationDefault($this->userPaymentCardDataRepository);
    }

    public function testHandle(): void
    {
        $userId = 1;
        $defaultPaymentCardId = 2;

        $this->userPaymentCardDataRepository
            ->expects($this->once())
            ->method('updateBy')
            ->with(['user_id' => $userId], ['is_default' => false]);

        $this->userPaymentCardDataRepository
            ->expects($this->once())
            ->method('update')
            ->with($defaultPaymentCardId, ['is_default' => true]);

        $expectedResult = new Collection();

        $this->userPaymentCardDataRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['user_id' => $userId])
            ->willReturn($expectedResult);

        $result = $this->useCase->handle($userId, $defaultPaymentCardId);

        $this->assertSame($expectedResult, $result);
    }
}
