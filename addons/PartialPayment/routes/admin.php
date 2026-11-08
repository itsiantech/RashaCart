<?php

use Addons\PartialPayment\Livewire\Admin\RefundsIndex;
use App\Http\Middleware\EnsureAddonActive;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/addons/partial-payment')
    ->middleware(['web', 'auth', 'vendor.approved', 'role:admin', EnsureAddonActive::class.':PartialPayment'])
    ->name('admin.addons.partial-payment.')
    ->group(function () {
        Route::get('/refunds', RefundsIndex::class)->name('refunds');
    });
