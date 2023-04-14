<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'show'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'destroy'])->name('logout');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [\App\Http\Controllers\HomeController::class, 'show'])->name('home');
    Route::get('/access', \App\Http\Livewire\AccessManager::class)->name('admin.access');
    Route::get('/tags', \App\Http\Livewire\TagManager::class)->name('admin.tags');
});
