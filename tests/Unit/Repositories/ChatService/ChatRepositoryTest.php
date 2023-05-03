<?php

namespace Tests\Unit\Repositories\ChatService;

use App\Models\Chat;
use App\Repositories\ChatService\ChatRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_chat_by_id()
    {
        $chat = Chat::factory()->create();

        $repository = new ChatRepository();

        $result = $repository->getById($chat->id);

        $this->assertInstanceOf(Chat::class, $result);
        $this->assertEquals($chat->id, $result->id);
    }

    /** @test */
    public function it_can_get_chat_by_game_id()
    {
        $gameId = 1;
        $chat = Chat::factory()->create(['game_id' => $gameId]);

        $repository = new ChatRepository();

        $result = $repository->getByGameId($gameId);

        $this->assertInstanceOf(Chat::class, $result);
        $this->assertEquals($chat->id, $result->id);
    }

    /** @test */
    public function it_returns_null_when_chat_does_not_exist_for_game_id()
    {
        $gameId = 1;

        $repository = new ChatRepository();

        $result = $repository->getByGameId($gameId);

        $this->assertNull($result);
    }
}
