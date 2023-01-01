<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $publisherTeamMemberRole = Role::factory()
            ->create([
                'name' => Role::ROLE_PUBLISHER_TEAM_MEMBER,
                'permissions' => ['game' => ['crud']],
            ]);

        Role::factory()
            ->create([
                'name' => Role::ROLE_CUSTOMER,
                'permissions' => ['game' => ['read']],
            ]);

        Role::factory()
            ->create([
                'name' => Role::ROLE_PUBLISHER,
                'permissions' => [
                    'game' => ['crud!'],
                    'team_members' => [
                        User::factory()->create(['role_id' => $publisherTeamMemberRole->id])->id,
                        User::factory()->create(['role_id' => $publisherTeamMemberRole->id])->id,
                    ],
                ],
            ]);

        Role::factory()
            ->create([
                'name' => Role::ROLE_ADMIN,
                'permissions' => ['game' => ['review!', 'crud!'], 'company' => ['review!']],
            ]);

        Role::factory()
            ->create([
                'name' => Role::ROLE_MODERATOR,
                'permissions' => ['game' => ['review', 'crud'], 'company' => ['review']],
            ]);
    }
}
