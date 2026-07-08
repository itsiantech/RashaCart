<?php

use App\Livewire\Rider\Dashboard;
use Illuminate\Support\Facades\Route;

Route::middleware(['role:rider'])->group(function () {

    // Route::prefix('rider')->name('rider.')->group(function () {
    //     Route::get('/dashboard', Dashboard::class)->name('dashboard');
    // });

});
