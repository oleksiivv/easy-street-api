<?php

namespace Tests\Unit\Repositories;

use App\Models\Administrator;
use App\Models\Role;
use App\Models\User;
use App\Repositories\AdministratorRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class AdministratorRepositoryTest extends TestCase
{
    use RefreshDatabase; // Reset the database before each test case

    /** @var AdministratorRepository */
    private $administratorRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->administratorRepository = app(AdministratorRepository::class);
    }

    /** @test */
    public function createOrUpdate_should_add_moderator_to_existing_administrator()
    {
        User::factory()->for(Role::factory())->create([
            'id' => 1,
        ]);

        // Arrange
        $moderatorEmail = 'test@example.com';

        // Act
        $result = $this->administratorRepository->createOrUpdate(
            $moderatorEmail,
            $moderatorEmail,
            1
        );

        // Assert
        $this->assertInstanceOf(Administrator::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertContains($moderatorEmail, $result->moderators);
    }

    /** @test */
    public function createOrUpdate_should_create_new_administrator_if_not_found()
    {
        User::factory()->for(Role::factory())->create([
            'id' => 1,
        ]);

        // Arrange
        $administratorEmail = 'test@example.com';
        $moderatorEmail = 'test2@example.com';
        $administratorId = 1;

        // Act
        $result = $this->administratorRepository->createOrUpdate($administratorEmail, $moderatorEmail, $administratorId);

        // Assert
        $this->assertInstanceOf(Administrator::class, $result);
        $this->assertContains($moderatorEmail, $result->moderators);
    }

    /** @test */
    public function getByAdministratorUserId_should_return_administrator()
    {
        // Arrange
        User::factory()->for(Role::factory())->create([
            'id' => 1,
        ]);
        $administrator = Administrator::create([
            'administrator_email' => 'test',
            'user_id' => 1,
            'moderators' => [],
        ]);

        // Act
        $result = $this->administratorRepository->getByAdministratorUserId($administrator->user_id);

        // Assert
        $this->assertInstanceOf(Administrator::class, $result);
        $this->assertEquals($administrator->id, $result->id);
    }

    /** @test */
    public function getByAdministratorUserId_should_throw_exception_if_not_found()
    {
        // Expect
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        // Act
        $this->administratorRepository->getByAdministratorUserId(999);
    }
}
