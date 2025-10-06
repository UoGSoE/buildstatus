<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/sso-auth.php';

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', \App\Livewire\HomePage::class)->name('home');
    Route::get('/machine/{machine}', \App\Livewire\MachineDetails::class)->name('machine.details');
});
