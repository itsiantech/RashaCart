<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\User;

#[Layout('livewire.auth.layouts.app')]
class Vendor extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required')]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate();

        if (! Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        session()->regenerate();

        $user = auth()->user();

        // Use Spatie role check
        if ($user->hasRole('vendor') && ! $user->is_vendor_approved) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Your vendor account is pending approval.',
            ]);
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('vendor')) {
            return redirect()->route('admin.vendor.profile', $user->id);
        }

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.auth.vendor-login');
    }
}
