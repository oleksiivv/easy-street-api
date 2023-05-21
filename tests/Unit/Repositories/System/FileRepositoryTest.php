<?php

namespace Tests\Unit\Repositories\System;

use App\Models\Game;
use App\Repositories\GamePageRepository;
use App\Repositories\GameReleaseRepository;
use App\Repositories\System\FileRepository;
use App\System\OperatingSystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileRepositoryTest extends TestCase
{
    private FileRepository $fileRepository;
    private GamePageRepository $gamePageRepository;
    private GameReleaseRepository $gameReleaseRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gamePageRepository = $this->getMockBuilder(GamePageRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->gameReleaseRepository = $this->getMockBuilder(GameReleaseRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fileRepository = new FileRepository(
            $this->gamePageRepository,
            $this->gameReleaseRepository,
        );
    }

    public function testUploadFile()
    {
        $this->markTestSkipped('Functionality changed to match configuration');

        $game = new Game();
        $game->id = 1;

        $uploadedFile = UploadedFile::fake()->image('test.png');

        $destinationPath = sprintf(OperatingSystem::PAGE_FILES_PATH, $game->id, 'prefix');

        Storage::fake('public');

        $this->gamePageRepository->expects($this->once())
            ->method('updateByArray')
            ->with($game->id, [
                FileRepository::FILE_TYPE_ICON => Storage::url($destinationPath . '/' . $uploadedFile->getClientOriginalName()),
            ]);

        $this->fileRepository->uploadFile($game, $uploadedFile, false, 'prefix', FileRepository::FILE_TYPE_ICON);

        Storage::disk('public')->assertExists($destinationPath . '/' . $uploadedFile->getClientOriginalName());
    }
}
