<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('livewire.auth.layouts.app')]
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('livewire.auth.login');
    }

    public function createVendor(): View
    {
        return view('livewire.auth.login');
    }

    public function createAdmin(): View
    {
        return view('livewire.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function googleLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleAuthentication()
    {

        try {
            $googleUserData = Socialite::driver('google')->user();
            $checkUser = User::where('google_id', $googleUserData->id)->first();

            if ($checkUser) {
                Auth::login($checkUser);

                return redirect()->route('home');
            } else {
                $user = User::create([
                    'google_id' => $googleUserData->id,
                    'name' => $googleUserData->name,
                    'email' => $googleUserData->email,
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                ]);

                $role = Role::where('name', 'user')->first();
                $user->assignRole($role);

                Auth::login($user);

                return redirect()->route('home');
            }
        } catch (\Exception $e) {
            $this->dispatch('error', message: $e->getMessage());
        }

    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user->role === 'customer' && ! $user->hasRole('customer')) {
            $user->assignRole('customer');
        }

        // Check if user is customer
        if ($user->role !== 'customer') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Please use the appropriate login portal.',
            ]);
        }

        return redirect()->intended(route('home'));
    }

    public function storeVendor(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user->role === 'vendor' && ! $user->hasRole('vendor')) {
            $user->assignRole('vendor');
        }

        // Check if user is vendor
        if ($user->role !== 'vendor') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Please use vendor login portal.',
            ]);
        }

        // Check if vendor is approved
        if (! $user->is_vendor_approved) {
            Auth::logout();

            return redirect()->route('vendor.login')
                ->withErrors(['email' => 'Your vendor account is pending approval.']);
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function storeAdmin(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user->role === 'admin' && ! $user->hasRole('admin')) {
            $user->assignRole('admin');
        }

        // Check if user is admin
        if ($user->role !== 'admin') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'You are not authorized to access admin panel.',
            ]);
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    // public function storeAdmin(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();
    //     $request->session()->regenerate();

    //     $user = Auth::user();
    //     if ($user) {
    //         return redirect()->intended(route('admin.dashboard', absolute: false));
    //     }else{
    //         abort(403, 'You do not have permission to access this area');
    //     }

    // }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');

    }
}
