<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/machine', [\App\Http\Controllers\Api\MachineUpdateController::class, 'store'])->middleware('auth:sanctum');
