<?php

use App\Http\Controllers\Frontend\CustomerAuthController;
use App\Http\Controllers\Frontend\CustomerInvoicePrintController;
use App\Livewire\Frontend\Customer\CustomerDashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer Auth + Dashboard routes (new file — no existing routes touched)
|--------------------------------------------------------------------------
*/

// JSON API for the header auth modal (rate limited on login).
Route::prefix('customer')->name('customer.')->group(function () {
    Route::post('/login', [CustomerAuthController::class, 'login'])
        ->middleware('throttle:10,1')->name('login');
    Route::post('/register', [CustomerAuthController::class, 'register'])
        ->middleware('throttle:10,1')->name('register');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

    // Forgot password: email link or phone OTP depending on admin setting.
    Route::post('/password/email', [CustomerAuthController::class, 'sendResetLink'])
        ->middleware('throttle:5,1')->name('password.email');
    Route::post('/password/phone-otp', [CustomerAuthController::class, 'sendPhoneOtp'])
        ->middleware('throttle:5,1')->name('password.phone-otp');
    Route::post('/password/phone-reset', [CustomerAuthController::class, 'resetWithPhoneOtp'])
        ->middleware('throttle:5,1')->name('password.phone-reset');

    // Google sign-in.
    Route::get('/auth/google', [CustomerAuthController::class, 'googleRedirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [CustomerAuthController::class, 'googleCallback'])->name('google.callback');

    // Complete missing phone after Google signup.
    Route::post('/phone', [CustomerAuthController::class, 'savePhone'])
        ->middleware('auth')->name('phone.save');
});

// Protected customer dashboard. 'customer' middleware alone: guests are
// redirected home with the auth popup auto-opened (auth middleware would
// bounce them to the admin-facing /login page instead).
Route::middleware(['customer'])->prefix('account')->name('customer.')->group(function () {
    Route::get('/{tab?}', CustomerDashboard::class)
        ->where('tab', 'profile|track|orders|payments|invoices|settings|overview')
        ->name('dashboard');

    // Print-friendly invoice (own orders only). ?auto=1 opens the print dialog.
    Route::get('/invoice/{order}/print', CustomerInvoicePrintController::class)
        ->name('invoice.print');
});
