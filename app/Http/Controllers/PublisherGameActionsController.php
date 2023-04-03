<?php

namespace App\Http\Controllers;

use App\Repositories\GameActionRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PublisherGameActionsController extends Controller
{
    public function __construct(private GameActionRepository $gameActionRepository)
    {
    }

    public function allForCompany(int $companyId): Response
    {
        return new Response($this->gameActionRepository->getAllUsersActions($companyId));
    }

    public function allForGame(int $gameId): Response
    {
        return new Response($this->gameActionRepository->getAllPublisherActionsForGame($gameId));
    }

    public function allPublisherActionsForGame(int $gameId): Response
    {
        return new Response($this->gameActionRepository->getAllUsersActionsForGame($gameId));
    }
}
