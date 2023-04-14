<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Machine;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UpdateMachineJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $requestData;

    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
    }

    public function handle(): void
    {
        $machine = Machine::firstOrCreate(['name' => $this->requestData['name']]);
        $machine->status = $this->requestData['status'];

        if (isset($this->requestData['started_at'])) {
            $machine->started_at = Carbon::parse($this->requestData['started_at']);
        }
        if (isset($this->requestData['finished_at'])) {
            $machine->finished_at = Carbon::parse($this->requestData['finished_at']);
        }
        if (isset($this->requestData['ip_address'])) {
            $machine->ip_address = $this->requestData['ip_address'];
        }

        $machine->save();

        if (!empty($this->requestData['tags'])) {
            $machine->tags()->sync([]);
            collect($this->requestData['tags'])->each(function ($tag) use ($machine) {
                $machine->tags()->firstOrCreate(['name' => trim($tag)]);
            });
        }
    }
}
