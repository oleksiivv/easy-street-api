<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGameRequest;
use App\Http\Requests\GetGamesRequest;
use App\Http\Requests\UpdateGameCategoryRequest;
use App\Http\Requests\UpdateGamePageRequest;
use App\Http\Requests\UpdateGamePaidProductRequest;
use App\Http\Requests\UpdateGameReleaseFilesRequest;
use App\Http\Requests\UpdateGameReleaseRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Requests\UpdateGameSecurityRequest;
use App\Repositories\DownloadsRepository;
use App\Repositories\GameCategoryRepository;
use App\Repositories\GameRepository;
use App\Repositories\LikesRepository;
use App\Repositories\System\FileRepository;
use App\Repositories\UserSubscriptionsRepository;
use App\System\OperatingSystem;
use App\UseCases\CreateGameUseCase;
use App\UseCases\UpdateGameUseCase;
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
    ) {
    }

    public function index(GetGamesRequest $getGamesRequest): Response
    {
        $data = $this->gameRepository->list();

        return new Response($data);
    }

    public function getGame(int $id): Response
    {
        $game = $this->gameRepository->get($id);

        return new Response($game);
    }

    public function gameStats(int $id): Response
    {
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

    public function createGame(CreateGameRequest $createGameRequest): Response
    {
        $game = $this->createGameUseCase->handle($createGameRequest->getGameDTO());

        return new Response($game);
    }

    public function releaseGame(int $gameId): Response
    {
        $game = $this->gameRepository->get($gameId);

        if (!$game->approved) {
            throw new HttpException(422, "Game hasn't been approved yet");
        }

        $game = $this->gameRepository->updateByArray($gameId, [
            'status' => 'active',
        ]);

        return new Response($game);
    }

    public function updateGame(int $gameId, UpdateGameRequest $updateGameRequest): Response
    {
        $game = $this->updateGameUseCase->handle($gameId, $updateGameRequest->getGameDTO());

        return new Response($game);
    }

    public function updateGameRelease(int $gameId, UpdateGameReleaseRequest $updateGameReleaseRequest): Response
    {
        $game = $this->updateGameUseCase->handle($gameId, $updateGameReleaseRequest->getGameDTO());

        return new Response($game);
    }

    public function updateGameReleaseFiles(int $gameId, UpdateGameReleaseFilesRequest $updateGameReleaseFilesRequest): Response
    {
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
        if (isset($updateGameReleaseFilesRequest->linux_file_url)) {
            $this->fileRepository->uploadFile($game, $updateGameReleaseFilesRequest->linux_file_url, true, OperatingSystem::OTHER, FileRepository::FILE_TYPE_RELEASE);
        }

        return new Response($game->refresh());
    }

    public function updateGamePage(int $gameId, UpdateGamePageRequest $updateGamePageRequest): Response
    {
        $game = $this->updateGameUseCase->handle($gameId, $updateGamePageRequest->getGameDTO());

        return new Response($game);
    }

    public function updateGameProduct(int $gameId, UpdateGamePaidProductRequest $updateGamePaidProductRequest): Response
    {
        $game = $this->updateGameUseCase->handle($gameId, $updateGamePaidProductRequest->getGameDTO());

        return new Response($game);
    }

    public function updateGameSecurity(int $gameId, UpdateGameSecurityRequest $updateGameSecurityRequest): Response
    {
        $game = $this->updateGameUseCase->handle($gameId, $updateGameSecurityRequest->getGameDTO());

        return new Response($game);
    }

    public function updateGameCategory(int $gameId, UpdateGameCategoryRequest $updateGameCategoryRequest): Response
    {
        $game = $this->updateGameUseCase->handle($gameId, $updateGameCategoryRequest->getGameDTO());

        return new Response($game);
    }
}