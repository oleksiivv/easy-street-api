<?php

namespace Tests\Unit\Repositories;

namespace Tests\Unit\Repositories;

use App\Models\CustomerGame;
use App\Models\Game;
use App\Repositories\CustomerGameRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerGameRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testGet()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CustomerGameRepository();

        // Create a new customer game
        $customerGame = CustomerGame::factory()->create();

        // Retrieve the customer game by ID
        $retrievedCustomerGame = $repository->get($customerGame->id);

        // Assert that the retrieved customer game matches the created customer game
        $this->assertInstanceOf(CustomerGame::class, $retrievedCustomerGame);
        $this->assertEquals($customerGame->id, $retrievedCustomerGame->id);
    }

    public function testGetThrowsModelNotFoundException()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CustomerGameRepository();

        // Try to retrieve a non-existent customer game
        $nonExistentCustomerGameId = 999;
        $this->expectException(ModelNotFoundException::class);

        // Call the get method
        $repository->get($nonExistentCustomerGameId);
    }

    public function testList()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CustomerGameRepository();

        // Create some customer games
        $customerGame1 = CustomerGame::factory()->create();
        $customerGame2 = CustomerGame::factory()->create();
        $customerGame3 = CustomerGame::factory()->create();

        // Retrieve the customer games list
        $customerGames = $repository->list();

        // Assert that the customer games list is a collection
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $customerGames);

        // Assert that the customer games list contains the created customer games
        $this->assertTrue($customerGames->contains('id', $customerGame1->id));
        $this->assertTrue($customerGames->contains('id', $customerGame2->id));
        $this->assertTrue($customerGames->contains('id', $customerGame3->id));
    }

    public function testCreate()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CustomerGameRepository();

        // Create customer game data
        $data = [
            'game_id' => Game::factory()->create()->id,
            'customer_id' => 1,
            // Add other required data fields
        ];

        // Create a new customer game
        $createdCustomerGame = $repository->create($data);

        // Assert that the created customer game matches the provided data
        $this->assertInstanceOf(CustomerGame::class, $createdCustomerGame);
        $this->assertEquals($data['game_id'], $createdCustomerGame->game_id);
        $this->assertEquals($data['customer_id'], $createdCustomerGame->customer_id);
        // Assert other fields if necessary
    }

    public function testUpdate()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CustomerGameRepository();

        // Create a new customer game
        $customerGame = CustomerGame::factory()->create();

        // Update the customer game data
        $updatedData = [
            'customer_id' => 2,
            // Update other fields if necessary
        ];
        $updatedCustomerGame = $repository->update($customerGame->id, $updatedData);

        // Assert that the updated customer game matches the provided data
        $this->assertInstanceOf(CustomerGame::class, $updatedCustomerGame);
        $this->assertEquals($customerGame->id, $updatedCustomerGame->id);
        $this->assertEquals($updatedData['customer_id'], $updatedCustomerGame->customer_id);
        // Assert other fields if necessary
    }

    public function testUpdateThrowsModelNotFoundException()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CustomerGameRepository();

        // Try to update a non-existent customer game
        $nonExistentCustomerGameId = 999;
        $this->expectException(ModelNotFoundException::class);

        // Call the update method
        $repository->update($nonExistentCustomerGameId, []);
    }

    public function testExists()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CustomerGameRepository();

        // Create a customer game
        $customerGame = CustomerGame::factory()->create();

        // Check if the customer game exists using its data
        $exists = $repository->exists([
            'game_id' => $customerGame->game_id,
            'customer_id' => $customerGame->customer_id,
        ]);

        // Assert that the customer game exists
        $this->assertNotEmpty($exists);
    }

    public function testUpdateOrCreate()
    {
        $this->markTestIncomplete("Depends on database content");

        $repository = new CustomerGameRepository();

        // Create customer game data
        $data = [
            'game_id' => Game::factory()->create()->id,
            'customer_id' => 1,
            // Add other required data fields
        ];

        // Update or create a customer game
        $repository->updateOrCreate($data, $data);

        // Assert that the customer game exists
        $this->assertGreaterThan(0, $repository->exists($data));
    }
}
