<?php

namespace Tests\Unit\UseCases;

use App\Events\FileUploadedEvent;
use App\Models\Game;
use App\Repositories\GameRepository;
use App\Repositories\System\FileRepository;
use App\UseCases\FileUploadUseCase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;
use function event;

class FileUploadUseCaseTest extends TestCase
{
    private FileUploadUseCase $useCase;
    private UploadedFile $file;
    private int $gameId;
    private string $pageFilePrefix;
    private string $fileType;
    private FileRepository $fileRepository;
    private GameRepository $gameRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up test data
        $this->gameId = 1;
        $this->pageFilePrefix = 'page_1';
        $this->fileType = 'text';

        // Create mock objects for dependencies
        $this->fileRepository = $this->createMock(FileRepository::class);
        $this->gameRepository = $this->createMock(GameRepository::class);
    }

    public function testHandleUploadsFileAndAssociatesWithGame(): void
    {
        $this->file = UploadedFile::fake()->create('test-file.txt');
        $this->useCase = new FileUploadUseCase($this->file, $this->gameId, $this->pageFilePrefix, $this->fileType);

        $game = new Game(); // Create a game object

        $this->gameRepository
            ->expects($this->once())
            ->method('get')
            ->with($this->gameId)
            ->willReturn($game);

        $this->fileRepository
            ->expects($this->once())
            ->method('uploadFile')
            ->with($game, $this->file, false, $this->pageFilePrefix, $this->fileType);

        $this->useCase->handle($this->fileRepository, $this->gameRepository);

        // Add assertions for file associations and event dispatch if needed
    }

    public function testHandleUploadsFileAndAssociatesWithGameAppFile(): void
    {
        $this->file = UploadedFile::fake()->create('test-file.apk');
        $this->useCase = new FileUploadUseCase($this->file, $this->gameId, $this->pageFilePrefix, $this->fileType);

        $game = new Game(); // Create a game object

        $this->gameRepository
            ->expects($this->once())
            ->method('get')
            ->with($this->gameId)
            ->willReturn($game);

        $this->fileRepository
            ->expects($this->once())
            ->method('uploadFile')
            ->with($game, $this->file, false, $this->pageFilePrefix, $this->fileType);

        $this->useCase->handle($this->fileRepository, $this->gameRepository);

        // Add assertions for file associations and event dispatch if needed
    }

    // Add more test methods for other scenarios

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
