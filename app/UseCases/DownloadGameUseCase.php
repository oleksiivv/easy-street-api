<?php

namespace App\UseCases;

use App\Events\GameDownloadedEvent;
use App\Models\Company;
use App\Models\Role;
use App\Models\UserPaymentCard;
use App\Repositories\CustomerGameRepository;
use App\Repositories\DownloadsRepository;
use App\Repositories\GameRepository;
use App\Repositories\UserPaymentCardDataRepository;
use App\Services\PaymentService;
use App\System\OperatingSystem;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class DownloadGameUseCase
{
    public function __construct(
        private CustomerGameRepository $customerGameRepository,
        private GameRepository $gameRepository,
        private DownloadsRepository $downloadsRepository,
        private PaymentService $paymentService,
        private UserPaymentCardDataRepository $userPaymentCardDataRepository,
    ) {
    }

    public function handle(int $gameId, int $customerId, string $os, ?array $paymentData): string
    {
        $game = $this->gameRepository->get($gameId);

        $alreadyDownloaded = $this->customerGameRepository->exists([
            'game_id' => $gameId,
            'user_id' => $customerId,
        ]);

        if ($game->paidProduct->price > 0 && ! $alreadyDownloaded) {
            if (! $this->proceedPayment($customerId, $paymentData, $game->paidProduct->price)) {
                throw new HttpException(422, 'Invalid payment data');
            }
        }

        $this->customerGameRepository->updateOrCreate(
            [
                'game_id' => $gameId,
                'user_id' => $customerId,
                'os' => strtok($os, '_'),
            ],
            [
                'downloaded' => true,
                'download_datetime' => now(),
                'version' => $game->gameReleases->last()->version,
                'os' => strtok($os, '_'),
            ]
        );

        $this->downloadsRepository->createIfNotExists([
            'game_id' => $gameId,
            'user_id' => $customerId,
        ]);

        event(new GameDownloadedEvent([
            'game_id' => $gameId,
            'user_id' => $customerId,
        ]));

        return $game->getReleaseByOs($os);
    }

    private function getFileExtension(string $os): string
    {
        return OperatingSystem::EXTENSIONS[$os];
    }

    private function proceedPayment(int $userId, ?array $paymentData, int $gamePrice): bool
    {
        $this->paymentService->init();

        try {
            $paymentCardData = $this->userPaymentCardDataRepository->findOrCreate($userId, array_merge(
                $paymentData['card'] ?? [],
                [
                    'address' => $paymentData['address'] ?? [],
                ]
            ));

            $user = $paymentCardData->user;

            $customer = $this->paymentService->createCustomer([
                'card' => [
                    'number' => $paymentCardData->number,
                    'cvc' => $paymentCardData->cvv,
                    'exp_month' => $paymentCardData->exp_month,
                    'exp_year' => $paymentCardData->exp_year
                ],
                'address' => $paymentCardData->address,
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
            ]);

            $this->paymentService->pay($customer->id, $gamePrice, []);
        } catch (Throwable $exception){
            throw $exception;
        }

        return true;
    }
}
