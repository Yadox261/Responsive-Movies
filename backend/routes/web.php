<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\MovieManager;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/admin', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/movies-admin', MovieManager::class)->name('movies.index');
    Route::get('/movies/create', \App\Livewire\MovieEditor::class)->name('movies.create');
    Route::get('/movies/{movie}/edit', \App\Livewire\MovieEditor::class)->name('movies.edit');

    Route::get('/users-admin', \App\Livewire\UserManager::class)->name('users.index');
    Route::get('/users/create', \App\Livewire\UserEditor::class)->name('users.create');
    Route::get('/users/{user}/edit', \App\Livewire\UserEditor::class)->name('users.edit');

    Route::get('/roles-admin', \App\Livewire\RoleManager::class)->name('roles.index');
    Route::get('/roles/create', \App\Livewire\RoleEditor::class)->name('roles.create');
    Route::get('/roles/{role}/edit', \App\Livewire\RoleEditor::class)->name('roles.edit');

    Route::get('/reservations-admin', \App\Livewire\ReservationManager::class)->name('reservations.index');
});
