<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Machine>
 */
class MachineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . $this->faker->randomNumber(3),
            'ip_address' => $this->faker->ipv4(),
            'started_at' => now()->subHour(rand(1, 10))->subMinute(rand(1, 50)),
            'finished_at' => null,
            'status' => 'Building',
        ];
    }
}
