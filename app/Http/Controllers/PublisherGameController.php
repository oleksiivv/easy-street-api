<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGameRequest;
use App\Http\Requests\GetGamesRequest;
use App\Http\Requests\UpdateGameCategoryRequest;
use App\Http\Requests\UpdateGameLinksRequest;
use App\Http\Requests\UpdateGamePageRequest;
use App\Http\Requests\UpdateGamePaidProductRequest;
use App\Http\Requests\UpdateGameReleaseFilesRequest;
use App\Http\Requests\UpdateGameReleaseRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Requests\UpdateGameSecurityRequest;
use App\Models\Game;
use App\Models\GameAction;
use App\Repositories\DownloadsRepository;
use App\Repositories\GameActionRepository;
use App\Repositories\GameCategoryRepository;
use App\Repositories\GameRepository;
use App\Repositories\LikesRepository;
use App\Repositories\System\FileRepository;
use App\Repositories\UserSubscriptionsRepository;
use App\Services\GameAccessService;
use App\System\OperatingSystem;
use App\UseCases\CreateGameUseCase;
use App\UseCases\UpdateGameUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PublisherGameController extends Controller
{
    public function __construct(
        private CreateGameUseCase $createGameUseCase,
        private UpdateGameUseCase $updateGameUseCase,
        private GameRepository $gameRepository,
        private FileRepository $fileRepository,
        private DownloadsRepository $downloadsRepository,
        private LikesRepository $likesRepository,
        private UserSubscriptionsRepository $userSubscriptionsRepository,
        private GameCategoryRepository $gameCategoryRepository,
        private GameActionRepository $gameActionRepository,
        private GameAccessService $gameAccessService,
    ) {
    }

    public function index(GetGamesRequest $getGamesRequest): Response
    {
        $data = $this->gameRepository->list();

        return new Response($data);
    }

    public function getGame(int $id, Request $request): Response
    {
        if ($this->gameAccessService->noAccess(data_get($request, 'user.id'), $id)) {
            throw new HttpException(422);
        }

        $game = $this->gameRepository->get($id);

        return new Response($game);
    }

    public function gameStats(int $id, Request $request): Response
    {
        if ($this->gameAccessService->noAccess(data_get($request, 'user.id'), $id)) {
            throw new HttpException(422);
        }

        $game = $this->gameRepository->get($id);

        return new Response([
            'downloads' => $this->downloadsRepository->getCountForGame($game->id),
            'likes_total' => $this->likesRepository->getCountForGame($game->id),
            'likes' => $this->likesRepository->getMiddle($game->id),
            'subscriptions' => $this->userSubscriptionsRepository->getCount($game->publisher->id),
            'game' => $game->toArray(),
            'last_release' => $game->gameReleases?->last()?->toArray(),
        ]);
    }

    public function getCategories(int $companyId): Response
    {
        $categories = $this->gameCategoryRepository->findBy([
            'company_id' => $companyId,
        ]);

        return new Response([
            'categories' => $categories,
        ]);
    }

    public function getGenres(int $companyId): Response
    {
        return new Response(Game::GENRES);
    }

    public function createGame(CreateGameRequest $createGameRequest): Response
    {
        $game = $this->createGameUseCase->handle($createGameRequest->getGameDTO());

        $this->gameActionRepository->create([
            'game_id' => $game->id,
            'type' => 'create',
            'fields' => [],
            'performed_by' => GameAction::PERFORMED_BY_COMPANY,
        ]);

        return new Response($game);
    }

    public function releaseGame(int $gameId, Request $request): Response
    {
        if ($this->gameAccessService->noAccess(data_get($request, 'user.id'), $gameId)) {
            throw new HttpException(422);
        }

        $game = $this->gameRepository->get($gameId);

        if (!$game->approved) {
            throw new HttpException(422, "Game hasn't been approved yet");
        }

        $game = $this->gameRepository->updateByArray($gameId, [
            'status' => 'active',
        ]);

        $this->gameActionRepository->create([
            'game_id' => $gameId,
            'type' => 'release',
            'fields' => ['status'],
            'performed_by' => GameAction::PERFORMED_BY_COMPANY,
        ]);

        return new Response($game);
    }

    public function releaseGameAsDemo(int $gameId, Request $request): Response
    {
        if ($this->gameAccessService->noAccess(data_get($request, 'user.id'), $gameId)) {
            throw new HttpException(422);
        }

        $game = $this->gameRepository->updateByArray($gameId, [
            'status' => 'demo',
        ]);

        $this->gameActionRepository->create([
            'game_id' => $gameId,
            'type' => 'release',
            'fields' => ['status'],
            'performed_by' => GameAction::PERFORMED_BY_COMPANY,
        ]);

        return new Response($game);
    }

    public function makeDraft(int $gameId, Request $request): Response
    {
        if ($this->gameAccessService->noAccess(data_get($request, 'user.id'), $gameId)) {
            throw new HttpException(422);
        }

        $game = $this->gameRepository->updateByArray($gameId, [
            'status' => 'draft',
        ]);

        $this->gameActionRepository->create([
            'game_id' => $gameId,
            'type' => 'release',
            'fields' => ['status'],
            'performed_by' => GameAction::PERFORMED_BY_COMPANY,
        ]);

        return new Response($game);
    }

    public function updateGame(int $gameId, UpdateGameRequest $updateGameRequest): Response
    {
        if ($this->gameAccessService->noAccess(data_get($updateGameRequest, 'user.id'), $gameId)) {
            throw new HttpException(422);
        }

        $game = $this->updateGameUseCase->handle($gameId, $updateGameRequest->getGameDTO());

        $this->gameActionRepository->create([
            'game_id' => $gameId,
            'type' => 'update',
            'fields' => [$updateGameRequest->getGameDTO()->toArray()],
            'performed_by' => GameAction::PERFORMED_BY_COMPANY,
        ]);

        $game->status = $game->status == 'active' ? 'update_in_review' : (
            $game->status == 'draft' ? 'draft' : (
                in_array($game->status, ['update_in_review', 'in_review']) ?
                    $game->status : 'ready_for_review'
            )
        );

        $game->save();
        $game->refresh();

        return new Response($game);
    }

    public function updateGameRelease(int $gameId, UpdateGameReleaseRequest $updateGameReleaseRequest): Response
    {
        if ($this->gameAccessService->noAccess(data_get($updateGameReleaseRequest, 'user.id'), $gameId)) {
            throw new HttpException(422);
        }

        $game = $this->updateGameUseCase->handle($gameId, $updateGameReleaseRequest->getGameDTO());

        $this->gameActionRepository->create([
            'game_id' => $gameId,
            'type' => 'update',
            'fields' => [$updateGameReleaseRequest->getGameDTO()->game_release_data->toArray()],
            'performed_by' => GameAction::PERFORMED_BY_COMPANY,
        ]);

        $game->status = $game->status === 'active' ? 'update_in_review' : (
            $game->status === 'draft' ? 'draft' : (
                in_array($game->status, ['update_in_review', 'in_review']) ?
                    $game->status : 'ready_for_review'
            )
        );

        $game->save();
        $game->refresh();

        return new Response($game);
    }

    public function updateGameReleaseFiles(int $gameId, UpdateGameReleaseFilesRequest $updateGameReleaseFilesRequest): Response
    {
        if ($this->gameAccessService->noAccess(data_get($updateGameReleaseFilesRequest, 'user.id'), $gameId)) {
            throw new HttpException(422);
        }

        $game = $this->gameRepository->get($gameId);

        Log::info(json_encode($updateGameReleaseFilesRequest->all(), JSON_PRETTY_PRINT));

        if (isset($updateGameReleaseFilesRequest->android_file_url)) {
            $this->fileRepository->uploadFile($game, $updateGameReleaseFilesRequest->android_file_url, true, OperatingSystem::ANDROID, FileRepository::FILE_TYPE_RELEASE);
        }
        if (isset($updateGameReleaseFilesRequest->ios_file_url)) {
            $this->fileRepository->uploadFile($game, $updateGameReleaseFilesRequest->ios_file_url, true, OperatingSystem::IOS, FileRepository::FILE_TYPE_RELEASE);
        }
        if (isset($updateGameReleaseFilesRequest->windows_file_url)) {
            $this->fileRepository->uploadFile($game, $updateGameReleaseFilesRequest->windows_file_url, true, OperatingSystem::WINDOWS, FileRepository::FILE_TYPE_RELEASE);
        }
        if (isset($updateGameReleaseFilesRequest->mac_file_url)) {
            $this->fileRepository->uploadFile($game, $updateGameReleaseFilesRequest->mac_file_url, true, OperatingSystem::MAC, FileRepository::FILE_TYPE_RELEASE);
        }
        if (isset($updateGameReleaseFilesRequest->linux_file_url)) {
            $this->fileRepository->uploadFile($game, $updateGameReleaseFilesRequest->linux_file_url, true, OperatingSystem::OTHER, FileRepository::FILE_TYPE_RELEASE);
        }

        $this->gameActionRepository->create([
            'game_id' => $gameId,
            'type' => 'update',
            'fields' => ['files'],
            'performed_by' => GameAction::PERFORMED_BY_COMPANY,
        ]);

        $game->status = $game->status === 'active' ? 'update_in_review' : (
            $game->status === 'draft' ? 'draft' : (
                in_array($game->status, ['update_in_review', 'in_review']) ?
                    $game->status : 'ready_for_review'
            )
        );

        $game->save();
        $game->refresh();

        return new Response($game->refresh());
    }

    public function updateGamePage(int $gameId, UpdateGamePageRequest $updateGamePageRequest): Response
    {
        if ($this->gameAccessService->noAccess(data_get($updateGamePageRequest, 'user.id'), $gameId)) {
            throw new HttpException(422);
        }

        $game = $this->updateGameUseCase->handle($gameId, $updateGamePageRequest->getGameDTO());

        $this->gameActionRepository->create([
            'game_id' => $gameId,
            'type' => 'update',
            'fields' => [$updateGamePageRequest->getGameDTO()->game_page_data->toArray()],
            'performed_by' => GameAction::PERFORMED_BY_COMPANY,
        ]);

        $game->status = $game->status === 'active' ? 'update_in_review' : (
            $game->status === 'draft' ? 'draft' : (
                in_array($game->status, ['update_in_review', 'in_review']) ?
                    $game->status : 'ready_for_review'
            )
        );

        $game->save();
        $game->refresh();

        return new Response($game);
    }

    public function updateGameLinks(int $gameId, UpdateGameLinksRequest $updateGameLinksRequest): Response
    {
        if ($this->gameAccessService->noAccess(data_get($updateGameLinksRequest, 'user.id'), $gameId)) {
            throw new HttpException(422);
        }

        $game = $this->updateGameUseCase->handle($gameId, $updateGameLinksRequest->getGameDTO());

        $this->gameActionRepository->create([
            'game_id' => $gameId,
            'type' => 'update',
            'fields' => [$updateGameLinksRequest->getGameDTO()->links->toArray()],
            'performed_by' => GameAction::PERFORMED_BY_COMPANY,
        ]);

        $game->status = $game->status === 'active' ? 'update_in_review' : 'in_review';
        $game->save();
        $game->refresh();

        return new Response($game);
    }

    public function updateGameProduct(int $gameId, UpdateGamePaidProductRequest $updateGamePaidProductRequest): Response
    {
        if ($this->gameAccessService->noAccess(data_get($updateGamePaidProductRequest, 'user.id'), $gameId)) {
            throw new HttpException(422);
        }

        $game = $this->updateGameUseCase->handle($gameId, $updateGamePaidProductRequest->getGameDTO());

        $this->gameActionRepository->create([
            'game_id' => $gameId,
            'type' => 'update',
            'fields' => [$updateGamePaidProductRequest->getGameDTO()->paid_product_data->toArray()],
            'performed_by' => GameAction::PERFORMED_BY_COMPANY,
        ]);

        $game->status = $game->status === 'active' ? 'update_in_review' : (
            $game->status === 'draft' ? 'draft' : (
                in_array($game->status, ['update_in_review', 'in_review']) ?
                    $game->status : 'ready_for_review'
            )
        );


        $game->save();
        $game->refresh();

        return new Response($game);
    }

    public function updateGameSecurity(int $gameId, UpdateGameSecurityRequest $updateGameSecurityRequest): Response
    {
        if ($this->gameAccessService->noAccess(data_get($updateGameSecurityRequest, 'user.id'), $gameId)) {
            throw new HttpException(422);
        }

        $game = $this->updateGameUseCase->handle($gameId, $updateGameSecurityRequest->getGameDTO());

        $this->gameActionRepository->create([
            'game_id' => $gameId,
            'type' => 'update',
            'fields' => [$updateGameSecurityRequest->getGameDTO()->game_security_data->toArray()],
            'performed_by' => GameAction::PERFORMED_BY_COMPANY,
        ]);

        $game->status = $game->status === 'active' ? 'update_in_review' : (
            $game->status === 'draft' ? 'draft' : (
                in_array($game->status, ['update_in_review', 'in_review']) ?
                    $game->status : 'ready_for_review'
            )
        );


        $game->save();
        $game->refresh();

        return new Response($game);
    }

    public function updateGameCategory(int $gameId, UpdateGameCategoryRequest $updateGameCategoryRequest): Response
    {
        if ($this->gameAccessService->noAccess(data_get($updateGameCategoryRequest, 'user.id'), $gameId)) {
            throw new HttpException(422);
        }

        $game = $this->updateGameUseCase->handle($gameId, $updateGameCategoryRequest->getGameDTO());

        $this->gameActionRepository->create([
            'game_id' => $gameId,
            'type' => 'update',
            'fields' => [$updateGameCategoryRequest->getGameDTO()->game_category_data->toArray()],
            'performed_by' => GameAction::PERFORMED_BY_COMPANY,
        ]);

        $game->status = $game->status === 'active' ? 'update_in_review' : (
            $game->status === 'draft' ? 'draft' : (
                in_array($game->status, ['update_in_review', 'in_review']) ?
                    $game->status : 'ready_for_review'
            )
        );

        $game->save();
        $game->refresh();

        return new Response($game);
    }
}
