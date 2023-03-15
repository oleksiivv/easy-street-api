<?php

namespace App\Repositories\System;

use App\Models\Game;
use App\Repositories\GamePageRepository;
use App\Repositories\GameReleaseRepository;
use App\Repositories\GameRepository;
use App\System\OperatingSystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileRepository
{
    public const FILE_TYPE_ICON = 'icon_url';

    public const FILE_TYPE_BACKGROUND = 'background_image_url';

    public const FILE_TYPE_DESCRIPTION_IMAGE = 'description_images';

    public const FILE_TYPE_RELEASE = 'release';

    public function __construct(
        private GamePageRepository $gamePageRepository,
        private GameReleaseRepository $gameReleaseRepository,
    ){
    }

    public function uploadFile(Game $game, UploadedFile $file, bool $appFile, string $pageFilePrefix, string $fileType)
    {
        $data = [
            'name' => $file->getClientOriginalName(),
            'file_extension' => $file->getClientOriginalExtension(),
            'original_path' => $file->getRealPath(),
        ];

        $destinationPath = $appFile
            ? sprintf(OperatingSystem::EXECUTABLE_FILES_PATH, $game->id, $file->getClientOriginalName())
            : sprintf(OperatingSystem::PAGE_FILES_PATH, $game->id, $pageFilePrefix);

        Storage::disk(env('STORAGE'))->put($destinationPath . '/' . $file->getClientOriginalName(), $file->getContent(), 'public');

        $path = Storage::disk(env('STORAGE'))->url($destinationPath . '/' . $file->getClientOriginalName());

        $data['path'] = $path;

         if ($fileType === self::FILE_TYPE_DESCRIPTION_IMAGE) {
            $descriptionImages = $game->gamePage->description_images;
            $descriptionImages[] = $path;

            $this->gamePageRepository->updateByArray($game->id, [
                $fileType => $descriptionImages,
            ]);
        } else if ($fileType != self::FILE_TYPE_RELEASE) {
            $this->gamePageRepository->updateByArray($game->id, [
                $fileType => $path,
            ]);
        } else {
             $this->gameReleaseRepository->updateByArray($game->gameReleases->last()->id, [
                 $pageFilePrefix => $path,
             ]);
         }

        return $data;
    }
}
