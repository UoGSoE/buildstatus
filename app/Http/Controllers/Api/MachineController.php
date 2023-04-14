<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Jobs\UpdateMachineJob;
use App\Http\Controllers\Controller;

class MachineController extends Controller
{
    public function store(Request $request)
    {
        $validatedRequestData = $request->validate([
            'name' => 'required',
            'status' => 'required|string',
            'started_at' => 'nullable|date_format:Y-m-d H:i:s',
            'finished_at' => 'nullable|date_format:Y-m-d H:i:s',
            'ip_address' => 'nullable|ip',
            'tags' => 'nullable|array',
        ]);

        UpdateMachineJob::dispatch($validatedRequestData);

        return ['message' => 'Machine updated'];
    }
}
