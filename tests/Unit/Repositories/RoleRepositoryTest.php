<?php

namespace Tests\Unit\Repositories;

use App\Models\Role;

class RoleRepositoryTest
{
    public function findByName(string $name): Role
    {
        return Role::where([
            'name' => $name,
        ])->firstOrFail();
    }

    public function get(int $id): Role
    {
        return Role::findOrFail($id);
    }
}
