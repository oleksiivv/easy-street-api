<?php

namespace Database\Factories;


use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RolesFactory extends Factory
{
    protected $model = Role::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
