<?php

namespace App\Console\Commands;

use App\Jobs\MachineUpdate;
use App\Models\Machine;
use Illuminate\Console\Command;

class SimulateApiUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:simulate-api-updates {--number=5 : Number of random machines to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate API updates for local development and demos';

    /**
     * Plausible machine status updates across different OS build systems.
     *
     * @var array<string>
     */
    protected array $statuses = [
        'Building',
        'Installing Updates',
        'Configuring',
        'Ready',
        'Pending Restart',
        'In Maintenance',
        'Provisioning',
        'Failed',
        'Imaging',
        'Testing',
        'Deploying Applications',
        'Awaiting Approval',
        'Offline',
        'Online',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $number = (int) $this->option('number');

        if ($number < 1) {
            $this->error('Number must be at least 1');

            return self::FAILURE;
        }

        $machines = Machine::inRandomOrder()->take($number)->get();

        if ($machines->isEmpty()) {
            $this->error('No machines found in the database. Run `lando mfs` to seed demo data.');

            return self::FAILURE;
        }

        $this->info("Simulating {$machines->count()} machine update(s)...");

        foreach ($machines as $machine) {
            $status = $this->statuses[array_rand($this->statuses)];

            $data = [
                'name' => $machine->name,
                'ip_address' => $machine->ip_address,
                'status' => $status,
                'notes' => $machine->notes,
                'lab_name' => $machine->lab?->name,
            ];

            MachineUpdate::dispatchSync($data);

            $this->line("Updated {$machine->name} → {$status}");
        }

        $this->newLine();
        $this->info('✓ Simulation complete!');

        return self::SUCCESS;
    }
}
