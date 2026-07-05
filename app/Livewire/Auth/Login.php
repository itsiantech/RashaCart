<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

#[Layout('livewire.auth.layouts.app')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();

        if (!Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        session()->regenerate();

        $user = Auth::user();

        // Check if vendor is approved (Spatie role check)
        if ($user->hasRole('vendor') && !$user->is_vendor_approved) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Your vendor account is pending approval.',
            ]);
        }

        // 🔑 ROLE-BASED REDIRECT (using Spatie hasRole)
        if ($user->hasRole('admin')) {
            $this->redirect(route('admin.dashboard'), navigate: true);
            return;
        }

        if ($user->hasRole('vendor')) {
            $this->redirect(route('admin.vendor.profile', $user->id), navigate: true);
            return;
        }

        // customer - regular user
        $this->redirect(route('home'), navigate: true);
    }



    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
    public function render()
    {
        return view('livewire.auth.login');
    }
}
