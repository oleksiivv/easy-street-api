<?php

namespace Tests\Unit\UseCases;

use App\DTO\GameCategoryDTO;
use App\DTO\GameDTO;
use App\DTO\GamePageDTO;
use App\Models\Company;
use App\Models\Game;
use App\Models\GameCategory;
use App\Repositories\CompanyRepository;
use App\Repositories\GameCategoryRepository;
use App\Repositories\GamePageRepository;
use App\Repositories\GameReleaseRepository;
use App\Repositories\GameRepository;
use App\Repositories\GameSecurityRepository;
use App\Repositories\PaidProductRepository;
use App\UseCases\CreateGameUseCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\Assert;

class CreateGameUseCaseTest extends TestCase
{
    private CreateGameUseCase $useCase;
    private GameRepository $gameRepository;
    private GameReleaseRepository $gameReleaseRepository;
    private GamePageRepository $gamePageRepository;
    private GameSecurityRepository $gameSecurityRepository;
    private GameCategoryRepository $gameCategoryRepository;
    private PaidProductRepository $paidProductRepository;
    private CompanyRepository $companyRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock objects for dependencies
        $this->gameRepository = $this->createMock(GameRepository::class);
        $this->gameReleaseRepository = $this->createMock(GameReleaseRepository::class);
        $this->gamePageRepository = $this->createMock(GamePageRepository::class);
        $this->gameSecurityRepository = $this->createMock(GameSecurityRepository::class);
        $this->gameCategoryRepository = $this->createMock(GameCategoryRepository::class);
        $this->paidProductRepository = $this->createMock(PaidProductRepository::class);
        $this->companyRepository = $this->createMock(CompanyRepository::class);

        // Create an instance of the CreateGameUseCase with the mock dependencies
        $this->useCase = new CreateGameUseCase(
            $this->gameRepository,
            $this->gameReleaseRepository,
            $this->gamePageRepository,
            $this->gameSecurityRepository,
            $this->gameCategoryRepository,
            $this->paidProductRepository,
            $this->companyRepository
        );
    }

    public function testHandleCreatesGameWithGameCategoryData(): void
    {
        $this->markTestIncomplete("Depends on database content");

        $gameDTO = new GameDTO();
        $gameDTO->game_category_data = new GameCategoryDTO(['name' => 'Action']);

        $gameCategory = new GameCategory();
        $gameCategory->id = 1;

        $this->gameCategoryRepository
            ->expects($this->once())
            ->method('createOrUpdate')
            ->with(null, $gameDTO->game_category_data)
            ->willReturn($gameCategory);

        $company = new Company();
        $company->full_type = true;
        $company->games = new Collection();

        $this->companyRepository
            ->expects($this->once())
            ->method('get')
            ->with($gameDTO->company_id)
            ->willReturn($company);

        $game = new Game();
        $game->id = 1;

        $this->gameRepository
            ->expects($this->once())
            ->method('create')
            ->with($gameDTO)
            ->willReturn($game);

        $result = $this->useCase->handle($gameDTO);

        $this->assertSame($game, $result);
    }

    public function testHandleCreatesGameWithGamePageData(): void
    {
        $this->markTestIncomplete("Depends on database content");
        $gameDTO = new GameDTO();
        $gameDTO->game_page_data = new GamePageDTO(['description' => 'Lorem ipsum']);

        $company = new Company();
        $company->full_type = true;
        $company->games = new Collection();

        $this->companyRepository
            ->expects($this->once())
            ->method('get')
            ->with($gameDTO->company_id)
            ->willReturn($company);

        $game = new Game();
        $game->id = 1;

        $this->gameRepository
            ->expects($this->once())
            ->method('create')
            ->with($gameDTO)
            ->willReturn($game);

        $this->gamePageRepository
            ->expects($this->once())
            ->method('create')
            ->with($gameDTO->game_page_data, $game->id);

        $result = $this->useCase->handle($gameDTO);

        $this->assertSame($game, $result);
    }

    // Add more test methods for the remaining scenarios

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->useCase = null;
        $this->gameRepository = null;
        $this->gameReleaseRepository = null;
        $this->gamePageRepository = null;
        $this->gameSecurityRepository = null;
        $this->gameCategoryRepository = null;
        $this->paidProductRepository = null;
        $this->companyRepository = null;
    }
}
