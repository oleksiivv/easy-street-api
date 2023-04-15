<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\FinancialEvent;
use App\Models\Payout;
use App\Repositories\CompanyRepository;
use App\Repositories\FinancialEventRepository;
use App\Repositories\PayoutsRepository;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Webmozart\Assert\Assert;

class PayoutsController extends Controller
{
    public function __construct(
        private FinancialEventRepository $financialEventRepository,
        private PayoutsRepository $payoutsRepository,
        private MailService $mailService,
        private CompanyRepository$companyRepository,
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

        $this->mailService->sendPayoutRequestConfirmation($payout->user->email, [
            'amount' => $payout->amount,
        ], 'Payout confirmed');

        return new Response($this->payoutsRepository->update($payoutId, [
            'status' => Payout::STATUS_CONFIRMED,
        ]));
    }
}
