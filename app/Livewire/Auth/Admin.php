<?php

namespace App\Livewire\Auth\Admin;

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Admin extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string|min:8|password')]
    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // check if user is admin
        if ($user->role !== 'admin') {
            throw ValidationException::withMessages([
                'email' => __('You do not have admin access.'),
            ]);
        }

        if (! $user->status) {
            throw ValidationException::withMessages([
                'email' => __('Your account is inactive. Please contact administrator.'),
            ]);
        }

        if (! auth()->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('admin.dashboard', absolute: false), navigate: true);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('livewire.auth.layouts.app');
    }
}
