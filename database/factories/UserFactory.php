<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'surname' => fake()->lastName(),
            'forenames' => fake()->firstName(),
            'is_staff' => false,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function staff(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_staff' => true,
            ];
        });
    }

    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_admin' => true,
            ];
        });
    }
}
