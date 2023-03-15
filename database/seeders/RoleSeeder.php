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
        Role::factory()
            ->create([
                'name' => Role::ROLE_PUBLISHER_TEAM_MEMBER,
                'permissions' => ['game' => ['crud']],
            ]);

        Role::factory()
            ->create([
                'name' => Role::ROLE_PUBLISHER,
                'permissions' => ['game' => ['crud'], 'company' => ['crud']],
            ]);

        Role::factory()
            ->create([
                'name' => Role::ROLE_CUSTOMER,
                'permissions' => ['game' => ['read']],
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
