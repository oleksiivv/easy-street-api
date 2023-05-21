<?php

namespace Tests\Unit\Repositories;

use App\Models\Download;
use App\Repositories\DownloadsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DownloadsRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateIfNotExists()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new DownloadsRepository();

        // Create download data
        $data = [
            'game_id' => 1,
            'user_id' => 1,
            // Add other required data fields
        ];

        // Create a new download
        $createdDownload = $repository->createIfNotExists($data);

        // Assert that the created download matches the provided data
        $this->assertInstanceOf(Download::class, $createdDownload);
        $this->assertEquals($data['game_id'], $createdDownload->game_id);
        $this->assertEquals($data['user_id'], $createdDownload->user_id);
        // Assert other fields if necessary

        // Try to create the same download again
        $duplicateDownload = $repository->createIfNotExists($data);

        // Assert that the duplicate download is the same as the previously created download
        $this->assertEquals($createdDownload->id, $duplicateDownload->id);
    }

    public function testGetCountForGame()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new DownloadsRepository();

        // Create some downloads for a game
        $gameId = 1;
        Download::factory()->count(3)->create(['game_id' => $gameId]);

        // Get the download count for the game
        $count = $repository->getCountForGame($gameId);

        // Assert that the count matches the number of created downloads
        $this->assertEquals(3, $count);
    }

    public function testExists()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new DownloadsRepository();

        // Create a download
        $gameId = 1;
        $userId = 1;
        Download::factory()->create(['game_id' => $gameId, 'user_id' => $userId]);

        // Check if the download exists using game and user IDs
        $exists = $repository->exists($gameId, $userId);

        // Assert that the download exists
        $this->assertTrue($exists);
    }

    public function testGetCountForCompany()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new DownloadsRepository();

        // Create some downloads for a company's games
        $companyId = 1;
        Download::factory()->count(2)->create([
            'game_id' => function () use ($companyId) {
                return Game::factory()->create(['company_id' => $companyId])->id;
            },
        ]);

        // Get the download count for the company
        $count = $repository->getCountForCompany($companyId);

        // Assert that the count matches the number of created downloads
        $this->assertEquals(2, $count);
    }
}
