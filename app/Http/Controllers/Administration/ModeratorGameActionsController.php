<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Repositories\GameActionRepository;
use Illuminate\Http\Response;

class ModeratorGameActionsController extends Controller
{
    public function __construct(private GameActionRepository $gameActionRepository)
    {
    }

    public function allForModerator(int $moderatorId): Response
    {
        return new Response($this->gameActionRepository->getAllModeratorActions($moderatorId));
    }

    public function allForAdministrator(int $administratorUserId): Response
    {
        return new Response($this->gameActionRepository->getAllAdminActions($administratorUserId));
    }

    public function allForModeratorByGame(int $gameId, int $moderatorId): Response
    {
        return new Response($this->gameActionRepository->getAllModeratorGameActions($moderatorId, $gameId));
    }
}

