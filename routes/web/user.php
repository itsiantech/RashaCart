<?php

use App\Livewire\User\Dashboard;
use Illuminate\Support\Facades\Route;

Route::middleware(['role:user'])->group(function () {

    // Route::prefix('user')->name('user.')->group(function () {
    //     Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // });

});
