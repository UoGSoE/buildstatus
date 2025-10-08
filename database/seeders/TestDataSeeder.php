<?php

namespace Database\Seeders;

use App\Models\Lab;
use App\Models\Log;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->staff()->admin()->create([
            'username' => 'admin2x',
            'email' => 'admin2x@example.com',
            'password' => Hash::make('secret'),
        ]);
        $staff = User::factory()->staff()->create([
            'username' => 'staff2x',
            'email' => 'staff2x@example.com',
            'password' => Hash::make('secret'),
        ]);
        $staff->createToken('api-token1');
        $staff->createToken('api-token2');
        $staff->createToken('api-token3');

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
