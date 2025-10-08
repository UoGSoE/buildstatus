<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/sso-auth.php';

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', \App\Livewire\HomePage::class)->name('home');
    Route::get('/machine/{machine}', \App\Livewire\MachineDetails::class)->name('machine.details');
    Route::get('/profile', \App\Livewire\Profile::class)->name('profile');

    Route::get('/admin/labs', \App\Livewire\Admin\ManageLabs::class)->name('admin.labs');
    Route::get('/admin/users', \App\Livewire\Admin\ManageUsers::class)->name('admin.users');
});
