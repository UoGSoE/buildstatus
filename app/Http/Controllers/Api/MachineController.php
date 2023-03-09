<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required|string',
            'started_at' => 'nullable|date_format:Y-m-d H:i:s',
            'finished_at' => 'nullable|date_format:Y-m-d H:i:s',
            'ip_address' => 'nullable|ip',
            'tags' => 'nullable|array',
        ]);

        $machine = \App\Models\Machine::firstOrCreate(['name' => $request->name]);

        $machine->status = $request->status;

        if ($request->started_at) {
            $machine->started_at = \Carbon\Carbon::parse($request->started_at);
        }
        if ($request->finished_at) {
            $machine->finished_at = \Carbon\Carbon::parse($request->finished_at);
        }
        if ($request->ip_address) {
            $machine->ip_address = $request->ip_address;
        }

        $machine->save();

        if ($request->filled('tags')) {
            $machine->tags()->sync([]);
            $request->collect('tags')->each(function ($tag) use ($machine) {
                $machine->tags()->firstOrCreate(['name' => trim($tag)]);
            });
        }

        return ['data' => $machine];
    }
}
