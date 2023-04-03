<?php

namespace App\UseCases;

use App\Models\Company;
use App\Models\Role;
use App\Repositories\CompanyRepository;
use App\Repositories\UserPaymentCardDataRepository;
use App\Repositories\UserRepository;
use App\Services\MailService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class CreateCompanyUseCase
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private PaymentService $paymentService,
        private MailService $mailService,
        private UserRepository $userRepository,
        private UserPaymentCardDataRepository $userPaymentCardDataRepository,
    ) {
    }

    public function handle(array $companyData, ?array $creditCardData): Company
    {
        try {
            $full = $companyData['type_full'] ?? false;

            $company = $this->companyRepository->create($companyData);

            if ($full) {
                $this->paymentService->init();

                $paymentCardData = $this->userPaymentCardDataRepository->findOrCreate($company->publisher->id, array_merge(
                    $creditCardData ?? [],
                    [
                        'address' => $company->address,
                        'is_default' => true,
                    ]
                ));

                $customer = $this->paymentService->createCustomer([
                    'card' => [
                        'number' => $paymentCardData->number,
                        'cvc' => $paymentCardData->cvc,
                        'exp_month' => $paymentCardData->exp_month,
                        'exp_year' => $paymentCardData->exp_year
                    ],
                    'address' => $paymentCardData->address,
                    'email' => $company->publisher->email,
                    'name' => $company->name . '#' . $company->publisher->email,
                ]);

                $this->paymentService->pay($customer->id, Company::ACCOUNT_PRICE, []);
            }

            $this->userRepository->updateRole($company->publisher->id, Role::ROLE_PUBLISHER);

            $this->mailService->sendCompanyCreatedConfirmation($company->publisher->email, [
                'name' => $company->publisher->name,
                'companyName' => $company->name,
            ], 'Company creating confirmation');

            return $company->refresh();
        } catch (ApiErrorException $exception){
            Log::info($exception->getMessage());
            throw new HttpException(422, "Couldn't proceed your payment. Please, try again.");
        } catch (Throwable $exception) {
            throw new HttpException(422, "Wrong input data. Couldn't create company.");
        }
    }
}
