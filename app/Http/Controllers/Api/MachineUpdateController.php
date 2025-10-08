<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMachineUpdateRequest;
use App\Jobs\MachineUpdate;

class MachineUpdateController extends Controller
{
    public function store(StoreMachineUpdateRequest $request)
    {
        MachineUpdate::dispatch($request->validated());

        return response()->json(['message' => 'Machine updated']);
    }
}
