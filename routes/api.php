<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/machine', function (Request $request) {
    $request->validate([
        'name' => 'required',
        'status' => 'required|string',
        'started_at' => 'nullable|date_format:Y-m-d H:i:s',
        'finished_at' => 'nullable|date_format:Y-m-d H:i:s',
        'ip_address' => 'nullable|ip',
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

    return ['data' => $machine];
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
