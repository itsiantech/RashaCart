<?php

use Addons\HelloWorld\Livewire\Admin\Dashboard;
use App\Http\Middleware\EnsureAddonActive;
use Illuminate\Support\Facades\Route;

// EnsureAddonActive makes the URL return 404 while the add-on is inactive,
// even if someone types it by hand.
Route::middleware(['web', 'auth', 'vendor.approved', 'role:admin', EnsureAddonActive::class.':HelloWorld'])
    ->prefix('admin/addons')
    ->name('admin.addons.')
    ->group(function () {
        Route::get('hello-world', Dashboard::class)->name('hello-world');
    });
