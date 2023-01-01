<?php

namespace Database\Factories;


use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsersFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'password_sha' => $this->faker->sha1(),
            'role_id' => $this->faker->randomElement(Role::pluck('id')),
        ];
    }
}
