<?php

namespace Tests\Unit\UseCases;

use App\DTO\GameDTO;
use App\Models\Company;
use App\Models\Game;
use App\Repositories\CompanyRepository;
use App\Repositories\GameCategoryRepository;
use App\Repositories\GamePageRepository;
use App\Repositories\GameReleaseRepository;
use App\Repositories\GameRepository;
use App\Repositories\GameSecurityRepository;
use App\Repositories\PaidProductRepository;
use Webmozart\Assert\Assert;

class CreateGameUseCaseTest
{
    public function __construct(
        private GameRepository $gameRepository,
        private GameReleaseRepository $gameReleaseRepository,
        private GamePageRepository $gamePageRepository,
        private GameSecurityRepository $gameSecurityRepository,
        private GameCategoryRepository $gameCategoryRepository,
        private PaidProductRepository $paidProductRepository,
        private CompanyRepository $companyRepository,
    ) {
    }

    public function handle(GameDTO $data): Game
    {
        if (isset($data->game_category_data)) {
            $data->game_category_id = $this->gameCategoryRepository->createOrUpdate($data->game_category_id, $data->game_category_data)->id;
        }

        $company = $this->companyRepository->get($data->company_id);

        $this->throwExceptionIfLimitExceeded($company);

        $game = $this->gameRepository->create($data);

        if (isset($data->game_page_data)) $this->gamePageRepository->create($data->game_page_data, $game->id);

        if (isset($data->game_release_data)) $this->gameReleaseRepository->create($data->game_release_data, $game->id);

        if (isset($data->game_security_data)) $this->gameSecurityRepository->create($data->game_security_data, $game->id);

        if (isset($data->paid_product_data)) $this->paidProductRepository->create($data->paid_product_data, $game->id);

        return $game->load(Game::RELATIONS)->refresh();
    }

    private function throwExceptionIfLimitExceeded(Company $company): void
    {
        if (!$company->full_type) {
            Assert::notEq($company->games->count(), Company::FREE_TYPE_GAMES_LIMIT);
        }
    }
}
