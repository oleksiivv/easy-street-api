<?php

namespace App\UseCases;

use App\Events\FileUploadedEvent;
use App\Repositories\GameRepository;
use App\Repositories\System\FileRepository;
use Illuminate\Http\UploadedFile;
use function event;

class FileUploadUseCase
{
    public function __construct(private UploadedFile $file, private int $gameId, private string $pageFilePrefix, private string $fileType)
    {
    }

    public function handle(FileRepository $fileRepository, GameRepository $gameRepository)
    {
        $game = $gameRepository->get($this->gameId);

        $fileRepository->uploadFile($game, $this->file, false, $this->pageFilePrefix, $this->fileType);

        //TODO: associate file with game

        //event(new FileUploadedEvent($fileData));
    }
}
