<?php

namespace App\Services;

use App\Repositories\GameRepository;
use App\Repositories\UserRepository;

class GameAccessService
{
    public function __construct(private GameRepository $gameRepository, private UserRepository $userRepository)
    {
    }

    public function noAccess(int $userId, int $gameId): bool
    {
        $game = $this->gameRepository->get($gameId);

        if ($game->publisher->publisher_id === $userId) {
            return false;
        }

        $user = $this->userRepository->get($userId);

        $moderators = $game->publisher->moderators;

        if (in_array($user->email, $moderators ?? [])) {
            return false;
        }

        return true;
    }
}
