<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddPublisherTeamMemberRequest;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\GetCompaniesRequest;
use App\Http\Requests\RemovePublisherTeamMemberRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Repositories\CompanyRepository;
use App\Repositories\DownloadsRepository;
use App\Repositories\LikesRepository;
use App\Repositories\UserSubscriptionsRepository;
use App\Services\PaymentService;
use App\UseCases\AddPublisherTeamMemberUseCase;
use App\UseCases\CreateCompanyUseCase;
use App\UseCases\RemovePublisherTeamMemberUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CompanyController extends Controller
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private AddPublisherTeamMemberUseCase $addPublisherTeamMemberUseCase,
        private RemovePublisherTeamMemberUseCase $removePublisherTeamMemberUseCase,
        private CreateCompanyUseCase $createCompanyUseCase,
        private DownloadsRepository $downloadsRepository,
        private LikesRepository $likesRepository,
        private UserSubscriptionsRepository $userSubscriptionsRepository,
    ) {
    }

    public function index(GetCompaniesRequest $getCompaniesRequest): Response
    {
        $companies = $this->companyRepository->list();

        return new Response($companies);
    }

    public function stats(int $companyId): Response
    {
        $company = $this->companyRepository->get($companyId);

        return new Response([
            'downloads' => $this->downloadsRepository->getCountForCompany($company->id),
            'likes' => $this->likesRepository->getMiddleForCompany($company->id),
            'likes_total' => $this->likesRepository->getCountForCompany($company->id),
            'subscriptions' => $this->userSubscriptionsRepository->getCount($company->id),
            'subscribers' => $this->userSubscriptionsRepository->list([
                'publisher_id' => $company->id,
            ]),
            'company' => $company->toArray(),
        ]);
    }

    public function subscribers(int $companyId)
    {
        return new Response([
            'subscribers' => $this->userSubscriptionsRepository->list([
                'publisher_id' => $companyId,
            ])
        ]);
    }

    public function get(int $id, Request $request): Response
    {
        $company = $this->companyRepository->get($id);

        return new Response($company);
    }

    public function create(CreateCompanyRequest $createCompanyRequest): Response
    {
        $company = $this->createCompanyUseCase->handle($createCompanyRequest->company, $createCompanyRequest->card);

        return new Response($company);
    }

    public function update(int $id, UpdateCompanyRequest $updateCompanyRequest): Response
    {
        $company = $this->companyRepository->update($id, $updateCompanyRequest->all());

        return new Response($company);
    }

    public function getTeam(int $id, Request $request): Response
    {
        $company = $this->companyRepository->get($id);

        return new Response([
            'publisher' => $company->publisher,
            'team_members' => $company->team_members,
        ]);
    }

    public function addTeamMember(int $companyId, AddPublisherTeamMemberRequest $request): Response
    {
        $company = $this->addPublisherTeamMemberUseCase->handle($companyId, $request->all());

        return new Response($company);
    }

    public function removeTeamMember(int $companyId, RemovePublisherTeamMemberRequest $request): Response
    {
        $company = $this->removePublisherTeamMemberUseCase->handle($companyId, $request->all());

        return new Response($company);
    }
}
