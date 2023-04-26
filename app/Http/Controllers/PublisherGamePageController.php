<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadBackgroundImageRequest;
use App\Http\Requests\UploadDescriptionImagesRequest;
use App\Http\Requests\UploadIconRequest;
use App\Repositories\System\FileRepository;
use App\Services\GameAccessService;
use App\System\OperatingSystem;
use App\UseCases\FileUploadUseCase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PublisherGamePageController extends Controller
{
    public function __construct(private GameAccessService $gameAccessService)
    {
    }

    public function uploadIcon(int $gameId, UploadIconRequest $uploadIconRequest): Response
    {
        if ($this->gameAccessService->noAccess(data_get($uploadIconRequest, 'user.id'), $gameId)) {
            throw new HttpException(422);
        }

        dispatch_sync(new FileUploadUseCase($uploadIconRequest->icon, $gameId, OperatingSystem::PAGE_FILE_PREFIX_FOR_ICON, FileRepository::FILE_TYPE_ICON));

        return response()->noContent();
    }

    public function uploadBackground(int $gameId, UploadBackgroundImageRequest $uploadBackgroundImageRequest): Response
    {
        if ($this->gameAccessService->noAccess(data_get($uploadBackgroundImageRequest, 'user.id'), $gameId)) {
            throw new HttpException(422);
        }

        dispatch_sync(new FileUploadUseCase($uploadBackgroundImageRequest->background_image, $gameId, OperatingSystem::PAGE_FILE_PREFIX_FOR_BACKGROUND, FileRepository::FILE_TYPE_BACKGROUND));

        return response()->noContent();
    }

    public function uploadDescriptionImages(int $gameId, UploadDescriptionImagesRequest $uploadDescriptionImagesRequest): Response
    {
        if ($this->gameAccessService->noAccess(data_get($uploadDescriptionImagesRequest, 'user.id'), $gameId)) {
            throw new HttpException(422);
        }

        foreach ($uploadDescriptionImagesRequest->description_images as $file) {
            dispatch_sync(new FileUploadUseCase($file, $gameId, OperatingSystem::PAGE_FILE_PREFIX_FOR_DESCRIPTION, FileRepository::FILE_TYPE_DESCRIPTION_IMAGE));
        }

        return response()->noContent();
    }
}
