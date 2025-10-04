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
        if ($this->data['lab_name']) {
            $lab = Lab::firstOrCreate([
                'name' => $this->data['lab_name'],
            ]);
        }

        $machineFields = [
            'name' => $this->data['name'],
        ];

        if ($this->data['ip_address']) {
            $machineFields['ip_address'] = $this->data['ip_address'];
        }

        if ($this->data['status']) {
            $machineFields['status'] = $this->data['status'];
        }

        if ($this->data['notes']) {
            $machineFields['notes'] = $this->data['notes'];
        }

        if ($lab) {
            $machineFields['lab_id'] = $lab->id;
        }

        $machine = Machine::updateOrCreate([
            'name' => $this->data['name'],
        ], $machineFields);

        $logMessage = [
            'message' => json_encode($machineFields),
            'format' => 'json',
        ];

        $machine->logs()->create([
            'message' => $logMessage,
            'format' => 'json',
        ]);
    }
}
