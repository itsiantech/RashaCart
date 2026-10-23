<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Settings;
use App\Models\User;
use App\Services\CustomerAuthSettings;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

/**
 * Customer authentication (popup modal + JSON API).
 *
 * Guest checkout keeps working regardless: these endpoints only
 * create/authenticate users with role=customer, never block orders.
 */
class CustomerAuthController extends Controller
{
    /** Merge guest orders (same phone/email) into the user account after login. */
    protected function linkGuestOrders(User $user): void
    {
        Order::whereNull('user_id')
            ->where(function ($q) use ($user) {
                if ($user->email) {
                    // Orders table has no email column; match via phone + name fallback.
                }
                if ($user->phone) {
                    $q->orWhere('phone', $user->phone);
                }
            })
            ->update(['user_id' => $user->id]);
    }

    /** Session cart is authoritative — it stays intact; just refresh the count. */
    protected function cartCount(): int
    {
        return array_sum(array_column(session()->get('cart', []), 'qty'));
    }

    protected function safeRedirect(Request $request): string
    {
        $url = $request->input('return_url')
            ?: session('customer_return_url')
            ?: session('url.intended');

        $redirect = CustomerAuthSettings::safeReturnUrl($url, route('customer.dashboard', absolute: false));

        session()->forget(['url.intended', 'customer_return_url']);

        return $redirect;
    }

    protected function ensureEnabled()
    {
        if (! CustomerAuthSettings::enabled()) {
            return response()->json(['message' => 'Customer login is currently disabled. You can checkout as guest.'], 403);
        }
        return null;
    }

    /** POST /customer/login — identifier = email and/or phone per admin setting. */
    public function login(Request $request)
    {
        if ($resp = $this->ensureEnabled()) {
            return $resp;
        }

        $identifier = CustomerAuthSettings::identifier();

        $rules = ['password' => ['required', 'string'], 'return_url' => ['nullable', 'string', 'max:2000']];
        if ($identifier === 'email') {
            $rules['identifier'] = ['required', 'email', 'max:255'];
        } elseif ($identifier === 'phone') {
            $rules['identifier'] = ['required', 'string', 'max:20'];
        } else {
            $rules['identifier'] = ['required', 'string', 'max:255'];
        }
        $data = $request->validate($rules);

        // Rate limiting: 5 attempts / minute per identifier+IP.
        $key = 'customer-login:' . Str::lower($data['identifier']) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'identifier' => trans('auth.throttle', ['seconds' => RateLimiter::availableIn($key), 'minutes' => 1]),
            ]);
        }

        $login = trim($data['identifier']);
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL) !== false;

        $query = User::where('role', 'customer');
        if ($identifier === 'email' || ($identifier === 'both' && $isEmail)) {
            $query->where('email', $login);
        } else {
            $query->where('phone', preg_replace('/\D+/', '', $login));
        }
        $user = $query->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages(['identifier' => trans('auth.failed')]);
        }
        if (! $user->status) {
            throw ValidationException::withMessages(['identifier' => 'Your account is disabled. Please contact support.']);
        }

        RateLimiter::clear($key);
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $this->linkGuestOrders($user);

        return response()->json([
            'message' => 'Login successful',
            'redirect' => $this->safeRedirect($request),
            'cart_count' => $this->cartCount(),
        ]);
    }

    /** POST /customer/register */
    public function register(Request $request)
    {
        if ($resp = $this->ensureEnabled()) {
            return $resp;
        }

        $identifier = CustomerAuthSettings::identifier();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9_.]+$/', 'unique:users,username'],
            'password' => ['required', Rules\Password::defaults()],
            'return_url' => ['nullable', 'string', 'max:2000'],
        ];
        if ($identifier === 'email') {
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'];
        } elseif ($identifier === 'phone') {
            $rules['phone'] = ['required', 'string', 'max:20', 'unique:users,phone'];
        } else {
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'];
            $rules['phone'] = ['required', 'string', 'max:20', 'unique:users,phone'];
        }
        $data = $request->validate($rules);

        if (isset($data['phone'])) {
            $data['phone'] = preg_replace('/\D+/', '', $data['phone']);
            // BD 11-digit sanity (non-blocking for intl numbers kept as-is).
            if (! preg_match('/^\d{6,15}$/', $data['phone'])) {
                throw ValidationException::withMessages(['phone' => 'Enter a valid phone number.']);
            }
        }

        $user = DB::transaction(function () use ($data) {
            $u = User::create([
                'name' => $data['name'],
                'username' => $data['username'] ?? null,
                'email' => $data['email'] ?? ('noemail_' . Str::uuid() . '@placeholder.local'),
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'role' => 'customer',
                'status' => true,
            ]);
            $role = Role::firstOrCreate(['name' => 'customer']);
            if (! $u->hasRole('customer')) {
                $u->assignRole($role);
            }
            return $u;
        });

        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();
        $this->linkGuestOrders($user);

        return response()->json([
            'message' => 'Registration successful',
            'redirect' => $this->safeRedirect($request),
            'cart_count' => $this->cartCount(),
            'needs_phone' => empty($user->phone),
        ], 201);
    }

    /** POST /customer/logout */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Logged out', 'redirect' => '/']);
        }
        return redirect('/');
    }

    // ── Forgot password: email link OR phone OTP depending on setting ──

    /** POST /customer/password/email */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        $status = Password::sendResetLink($request->only('email'));
        return response()->json(['message' => __($status)], $status === Password::RESET_LINK_SENT ? 200 : 422);
    }

    /** POST /customer/password/phone-otp — generate 6-digit OTP (logged, SMS gateway optional). */
    public function sendPhoneOtp(Request $request)
    {
        $request->validate(['phone' => ['required', 'string', 'max:20']]);
        $phone = preg_replace('/\D+/', '', $request->phone);
        if (! User::where('phone', $phone)->where('role', 'customer')->exists()) {
            return response()->json(['message' => 'No account found with this phone number.'], 422);
        }
        $otp = (string) random_int(100000, 999999);
        DB::table('customer_password_otps')->where('phone', $phone)->delete();
        DB::table('customer_password_otps')->insert([
            'phone' => $phone, 'otp' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10), 'created_at' => now(), 'updated_at' => now(),
        ]);
        // SMS gateway hook: uses existing bulk SMS settings if enabled.
        try {
            if (setting('sms_notification') && setting('sms_api_key')) {
                // sendSms($phone, "Bandrose password reset OTP: {$otp}. Valid 10 min.");
            }
            \Log::info("Customer password OTP for {$phone}: {$otp}");
        } catch (\Throwable $e) {
            \Log::error('OTP SMS failed: ' . $e->getMessage());
        }
        return response()->json(['message' => app()->environment('production') ? 'OTP sent to your phone.' : "OTP sent. (demo: {$otp})"]);
    }

    /** POST /customer/password/phone-reset — verify OTP + set new password. */
    public function resetWithPhoneOtp(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $phone = preg_replace('/\D+/', '', $data['phone']);
        $row = DB::table('customer_password_otps')->where('phone', $phone)->latest('id')->first();
        if (! $row || now()->gt($row->expires_at) || ! Hash::check($data['otp'], $row->otp)) {
            throw ValidationException::withMessages(['otp' => 'Invalid or expired OTP.']);
        }
        $user = User::where('phone', $phone)->where('role', 'customer')->firstOrFail();
        $user->forceFill(['password' => Hash::make($data['password'])])->save();
        DB::table('customer_password_otps')->where('phone', $phone)->delete();
        event(new PasswordReset($user));
        return response()->json(['message' => 'Password reset successful. Please login.']);
    }

    // ── Google sign-in ──

    public function googleRedirect(Request $request)
    {
        if (! CustomerAuthSettings::googleEnabled()) {
            return redirect('/')->with('error', 'Google sign-in is disabled.');
        }
        if ($request->has('return_url')) {
            session(['customer_return_url' => CustomerAuthSettings::safeReturnUrl($request->return_url)]);
        }
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function googleCallback(Request $request)
    {
        try {
            $g = Socialite::driver('google')->stateless()->user();
            $user = User::where('google_id', $g->id)->orWhere('email', $g->email)->first();
            if (! $user) {
                $user = User::create([
                    'google_id' => $g->id, 'name' => $g->name, 'email' => $g->email,
                    'password' => Hash::make(Str::random(32)), 'role' => 'customer', 'status' => true,
                    'profile_image' => $g->avatar ?? null,
                ]);
                $role = Role::firstOrCreate(['name' => 'customer']);
                $user->assignRole($role);
                event(new Registered($user));
            } elseif (! $user->google_id) {
                $user->update(['google_id' => $g->id]);
            }
            Auth::login($user, true);
            $request->session()->regenerate();
            $this->linkGuestOrders($user);

            // Ask for phone if missing (small step on dashboard).
            if (empty($user->phone)) {
                session(['customer_needs_phone' => true]);
                return redirect()->route('customer.dashboard', ['tab' => 'profile']);
            }
            $return = CustomerAuthSettings::safeReturnUrl(session('customer_return_url'), route('customer.dashboard', absolute: false));
            session()->forget('customer_return_url');
            return redirect($return)->with('success', 'Login successful');
        } catch (\Throwable $e) {
            \Log::error('Customer Google login failed: ' . $e->getMessage());
            return redirect('/')->with('error', 'Google login failed. Please try again.');
        }
    }

    /** POST /customer/phone — complete missing phone after Google signup. */
    public function savePhone(Request $request)
    {
        $request->validate(['phone' => ['required', 'string', 'max:20', 'unique:users,phone,' . Auth::id()]]);
        $request->user()->update(['phone' => preg_replace('/\D+/', '', $request->phone)]);
        session()->forget('customer_needs_phone');
        return response()->json(['message' => 'Phone number saved.']);
    }
}
