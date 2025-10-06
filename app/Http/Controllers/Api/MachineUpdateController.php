<?php

namespace App\Http\Controllers\Api;

use App\Models\Lab;
use App\Models\Machine;
use App\Http\Controllers\Controller;
use App\Jobs\MachineUpdate;
use Illuminate\Http\Request;

class MachineUpdateController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'lab_name' => 'nullable|string|max:255',
        ]);

        MachineUpdate::dispatch($validated);

        return response()->json(['message' => 'Machine updated']);
    }
}
