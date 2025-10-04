<?php

namespace Database\Seeders;

use App\Models\Lab;
use App\Models\Log;
use App\Models\User;
use App\Models\Machine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'username' => 'admin2x',
            'is_staff' => true,
            'email' => 'admin2x@example.com',
            'password' => Hash::make('secret'),
        ]);

        Lab::factory(10)->create();
        Lab::all()->each(function ($lab) {
            $machines = Machine::factory(rand(10, 100))->create([
                'lab_id' => $lab->id,
            ]);
            $machines->each(function ($machine) {
                Log::factory(rand(10, 100))->create([
                    'machine_id' => $machine->id,
                ]);
            });
        });
    }
}
