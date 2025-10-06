<?php

namespace App\Jobs;

use App\Models\Lab;
use App\Models\Machine;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class MachineUpdate implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $data)
    {
    }

    public function handle(): void
    {
        $lab = null;
        if ($this->data['lab_name'] ?? null) {
            $lab = Lab::firstOrCreate([
                'name' => $this->data['lab_name'],
            ]);
        }

        $machineFields = array_filter([
            'name' => $this->data['name'],
            'ip_address' => $this->data['ip_address'] ?? null,
            'status' => $this->data['status'] ?? null,
            'notes' => $this->data['notes'] ?? null,
            'lab_id' => $lab?->id,
        ], fn ($value) => $value !== null);

        $machine = Machine::updateOrCreate([
            'name' => $this->data['name'],
        ], $machineFields);

        $machine->logs()->create([
            'message' => json_encode($machineFields),
            'format' => 'json',
        ]);
    }
}
