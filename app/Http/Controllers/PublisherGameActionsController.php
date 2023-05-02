<?php

namespace App\Http\Controllers;

use App\Repositories\GameActionRepository;
use App\Services\CompanyAccessService;
use App\Services\GameAccessService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PublisherGameActionsController extends Controller
{
    public function __construct(
        private GameActionRepository $gameActionRepository,
        private GameAccessService $gameAccessService,
        private CompanyAccessService $companyAccessService,
    ) {
    }

    public function allForCompany(int $companyId, Request $request): Response
    {
        if ($this->companyAccessService->noAccess(data_get($request, 'user.id'), $companyId)) {
            throw new HttpException(401);
        }
        return new Response($this->gameActionRepository->getAllUsersActions($companyId));
    }

    public function allForGame(int $gameId, Request $request): Response
    {
        if ($this->gameAccessService->noAccess(data_get($request, 'user.id'), $gameId)) {
            throw new HttpException(401);
        }

        return new Response($this->gameActionRepository->getAllPublisherActionsForGame($gameId));
    }

    public function allPublisherActionsForGame(int $gameId, Request $request): Response
    {
        if ($this->gameAccessService->noAccess(data_get($request, 'user.id'), $gameId)) {
            throw new HttpException(401);
        }

        return new Response($this->gameActionRepository->getAllUsersActionsForGame($gameId));
    }
}
