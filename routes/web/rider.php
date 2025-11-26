<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Livewire\Rider\Dashboard;

Route::middleware(['role:rider'])->group(function () {
    
    // Route::prefix('rider')->name('rider.')->group(function () {
    //     Route::get('/dashboard', Dashboard::class)->name('dashboard');
    // });
    

});
    

    

    



