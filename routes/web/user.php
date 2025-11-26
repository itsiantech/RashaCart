<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;

use App\Livewire\User\Dashboard;



Route::middleware(['role:user'])->group(function () {

    // Route::prefix('user')->name('user.')->group(function () {
    //     Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // });
    

});

    



