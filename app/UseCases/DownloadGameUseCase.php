<?php

namespace App\UseCases;

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
        private FinancialEventRepository $financialEventRepository,
    ) {
    }

    public function handle(int $gameId, int $customerId, string $os, ?array $paymentData): string
    {
        $game = $this->gameRepository->get($gameId);

        $alreadyDownloaded = $this->customerGameRepository->exists([
            'game_id' => $gameId,
            'user_id' => $customerId,
        ]);

        $game->es_index = $game->es_index + 30;

        if ($game->paidProduct->price > 0 && ! $alreadyDownloaded) {
            if (! $this->proceedPayment($customerId, $paymentData, $game->paidProduct->price, $game)) {
                throw new HttpException(422, 'Invalid payment data');
            }

            $game->es_index = $game->es_index + 50;
        }

        $game->save();

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
            'category_id' => $game->gameCategory->id,
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

    private function proceedPayment(int $userId, ?array $paymentData, int $gamePrice, Game $game): bool
    {
        $this->paymentService->init();

        try {
            $paymentCardData = $this->userPaymentCardDataRepository->findOrCreate($userId, array_merge(
                $paymentData['card'] ?? [],
                [
                    'address' => $paymentData['address'] ?? [],
                    'is_default' => true,
                ]
            ));

            $user = $paymentCardData->user;

            $customer = $this->paymentService->createCustomer([
                'card' => [
                    'number' => $paymentCardData->number,
                    'cvc' => $paymentCardData->cvc,
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

        $this->financialEventRepository->create([
            'amount' => $gamePrice / 2,
            'partner_type' => FinancialEvent::PARTNER_TYPE_ES,
            'admin_id' => Administrator::firstOrFail()->id,
        ]);

        $this->financialEventRepository->create([
            'amount' => $gamePrice / 2,
            'partner_type' => FinancialEvent::PARTNER_TYPE_PUBLISHER,
            'company_id' => $game->publisher->publisher_id,
        ]);

        return true;
    }
}
