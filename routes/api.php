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
        'started_at' => 'required|date_format:Y-m-d H:i:s',
        'finished_at' => 'nullable|date_format:Y-m-d H:i:s',
        'status' => 'required|string',
        'ip_address' => 'nullable|ip',
    ]);
    $machine = \App\Models\Machine::firstOrCreate(['name' => $request->name]);
    $machine->update($request->only(['started_at', 'finished_at', 'status', 'ip_address']));

    return ['data' => $machine];
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
