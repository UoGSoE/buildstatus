<?php

namespace Database\Factories;

use App\Models\Lab;
use App\Models\Machine;
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
            'name' => $this->fakeHostname(),
            'ip_address' => fake()->ipv4(),
            'status' => fake()->randomElement(['online', 'offline', 'building', 'patching', 'OS Updateing']),
            'notes' => rand(0, 3) ? fake()->text() : null,
            'lab_id' => Lab::factory(),
        ];
    }

    protected function fakeHostname(): string
    {
        $subDomains = [
            'eng',
            'cs',
            'math',
            'phas',
            'chem',
            'ges',
        ];

        $subDomain = $subDomains[array_rand($subDomains)];
        $domain = 'example.ac.uk';
        $host = fake()->unique()->firstName();
        $hostname = $host . '.' . $subDomain . '.' . $domain;

        return $hostname;
    }
}
