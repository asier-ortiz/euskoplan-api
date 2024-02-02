<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class UserFactory extends Factory
{

    public function definition(): array
    {
        return [
            'username' => 'test',
            'email' => 'test@test.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ];
    }

    public function unverified()
    {
//        return $this->state(fn (array $attributes) => [
//            'email_verified_at' => null,
//        ]);
    }
}
