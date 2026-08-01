<?php

namespace App\Livewire\Admin\Settings\User\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileIndex extends Component
{
    use WithFileUploads;

    public $name;

    public $email;

    public $phone;

    public $address;

    public $profile_image;

    public $current_password;

    public $password;

    public $password_confirmation;

    public $accountActivation = false;

    public $delete_password;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->address = $user->address;
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->address = $this->address;

        if ($this->profile_image) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $this->profile_image->store('uploads/user-profile', 'public');
            $user->profile_image = $path;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('toast', message: 'Profile updated successfully!', type: 'success');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($this->password);
        $user->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->dispatch('toast', message: 'Password updated successfully!', type: 'success');
    }

    public function deleteAccount()
    {
        if (Auth::user()->hasRole('admin')) {
            $this->dispatch('toast', message: 'Super Admin cannot be deleted!', type: 'error');

            return;
        }

        $this->validate([
            'accountActivation' => 'accepted',
            'delete_password' => ['required', 'current_password'],
        ], [
            'accountActivation.accepted' => 'You must confirm account deactivation.',
        ]);

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }

    public function render()
    {
        return view('livewire.admin.settings.user.profile.profile-index', [
            'user' => Auth::user(),
        ])->layout('livewire.admin.layouts.app');
    }
}
