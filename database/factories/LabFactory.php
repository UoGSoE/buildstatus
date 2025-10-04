<?php

namespace Database\Factories;

use App\Models\Lab;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lab>
 */
class LabFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->fakeName(),
            'notes' => rand(0, 3) ? fake()->text() : null,
        ];
    }

    protected function fakeName(): string
    {
        $subDomains = [
            'Eng',
            'Cs',
            'Math',
            'Phas',
            'Chem',
            'Bio',
            'Ges',
        ];

        $subDomain = $subDomains[array_rand($subDomains)];
        $labname = $subDomain . str_pad(random_int(1, 100), 2, '0', STR_PAD_LEFT);

        return $labname;
    }
}
