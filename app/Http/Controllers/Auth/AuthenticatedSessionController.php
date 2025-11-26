<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    
    public function googleLogin(){
        return Socialite::driver('google')->redirect();
    }
    
    public function googleAuthentication(){

        try {
            $googleUserData = Socialite::driver('google')->user();
            $checkUser = User::where('google_id', $googleUserData->id)->first();
        
            if ($checkUser) {
                Auth::login($checkUser);
                return redirect()->route('home');
            } else {
                $user = User::create([
                    'google_id' =>  $googleUserData->id,
                    'name' =>  $googleUserData->name,
                    'email' =>  $googleUserData->email,
                    'password' =>  Hash::make('password')
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
        if ($user) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }else{
            abort(403, 'You do not have permission to access this area');
        }
        
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();        
        
        return redirect()->route('login');
       
    }

}
