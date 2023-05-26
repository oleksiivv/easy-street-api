<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\FinancialEvent;
use App\Models\Game;
use App\Models\Payout;
use App\Repositories\CompanyRepository;
use App\Repositories\FinancialEventRepository;
use App\Repositories\PayoutsRepository;
use App\Repositories\UserPaymentCardDataRepository;
use App\Services\MailService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;
use Webmozart\Assert\Assert;

class PayoutsController extends Controller
{
    public function __construct(
        private FinancialEventRepository $financialEventRepository,
        private PayoutsRepository $payoutsRepository,
        private MailService $mailService,
        private CompanyRepository $companyRepository,
        private PaymentService $paymentService,
        private UserPaymentCardDataRepository $userPaymentCardDataRepository,
    )
    {
    }

    public function all(Request $request): Response
    {
        return new Response($this->payoutsRepository->list([
            'status' => $request->status
        ]));
    }

    public function get(int $id): Response
    {
        return new Response($this->payoutsRepository->get($id));
    }

    public function create(Request $request): Response
    {
        if ($request->partner_type === FinancialEvent::PARTNER_TYPE_PUBLISHER) {
            Assert::lessThanEq($request->amount, $this->financialEventRepository->getTotalAmountByCompanyId($request->company_id));

            $this->financialEventRepository->create([
                'amount' => -$request->amount,
                'partner_type' => FinancialEvent::PARTNER_TYPE_PUBLISHER,
                'company_id' => $request->user_id,
            ]);

            $this->payoutsRepository->create([
                'amount' => $request->amount,
                'user_id' => $request->user_id,
                'status' => Payout::STATUS_IN_REVIEW,
            ]);

            $this->mailService->sendPayoutRequestCreation(Administrator::firstOrFail()->user->email, [
                'company' => $this->companyRepository->get($request->company_id)->toArray(),
                'amount' => $request->amount,
            ], 'New payout request');
        } else {
            Assert::lessThanEq($request->amount, $this->financialEventRepository->getTotalAmountByAdminId($request->admin_id));

            $this->financialEventRepository->create([
                'amount' => -$request->amount,
                'partner_type' => FinancialEvent::PARTNER_TYPE_ES,
                'admin_id' => $request->admin_id,
            ]);

            $this->payoutsRepository->create([
                'amount' => $request->amount,
                'user_id' => $request->user_id,
                'status' => Payout::STATUS_CONFIRMED,
            ]);

            $this->mailService->sendPayoutRequestConfirmation(Administrator::firstOrFail()->user->email, [
                'amount' => $request->amount,
            ], 'Payout confirmed');
        }

        return response()->noContent();
    }

    public function getByUserId(int $userId): Response
    {
        return new Response($this->payoutsRepository->getByUserId($userId));
    }

    public function approvePayout(int $payoutId): Response
    {
        $payout = $this->payoutsRepository->get($payoutId);

        //$this->proceedPayment($payout->user->id, [], -$payout->amount);

        $this->mailService->sendPayoutRequestConfirmation($payout->user->email, [
            'amount' => $payout->amount,
        ], 'Payout confirmed');

        return new Response($this->payoutsRepository->update($payoutId, [
            'status' => Payout::STATUS_CONFIRMED,
        ]));
    }

    private function proceedPayment(int $userId, ?array $paymentData, int $amount): bool
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

            $this->paymentService->pay($customer->id, $amount, []);
        } catch (Throwable $exception){
            dd($exception->getMessage());
            throw $exception;
        }

        return true;
    }

}
