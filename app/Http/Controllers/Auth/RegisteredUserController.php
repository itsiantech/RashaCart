<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('livewire.auth.layouts.app')]
class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Display vendor registration view.
     */
    public function createVendor(): View
    {
        return view('auth.register-vendor');
    }

    /**
     * Handle customer registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => 'required|numeric|digits:11',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', // Add role field
        ]);

        // Assign Customer Role (use 'name' column from Spatie package)
        $role = Role::where('name', 'customer')->first();
        if (! $role) {
            $role = Role::create(['name' => 'customer']);
        }
        $user->assignRole($role);

        // Give customer permissions if needed
        $permissions = [
            'view products',
            'place orders',
            'view own orders',
            'edit own profile',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $user->givePermissionTo($permissions);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('home'));
    }

    /**
     * Handle vendor registration request.
     */
    public function storeVendor(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|digits:11',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'shop_name' => 'required|string|max:255|unique:vendor_details,shop_name',
            'shop_address' => 'required|string|max:500',
            'NID_number' => 'required|string|max:50',
            'profile_image' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5048',
            'trade_license_front' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5048',
            'trade_license_back' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5048',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'payment_method' => 'nullable|string',
            'bank_name' => 'required_if:payment_method,bank|nullable|string|max:255',
            'bank_account_number' => 'required_if:payment_method,bank|nullable|string|max:50',
            'account_holder_name' => 'required_if:payment_method,bank|nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => false,
                'is_vendor_approved' => false,
            ]);

            $user->assignRole('vendor');
            $permissions = [
                'upload own products',
                'view own products',
                'edit own profile',
                'view vendor dashboard',
            ];

            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }

            $user->givePermissionTo($permissions);

            $profilePath = null;

            if ($request->hasFile('profile_image')) {
                $profilePath = $request->file('profile_image')
                    ->store('profile_images', 'public');
            }

            $frontPath = $request->file('trade_license_front')
                ->store('trade_licenses', 'public');

            $backPath = $request->file('trade_license_back')
                ->store('trade_licenses', 'public');

            $user->vendorDetails()->create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'shop_name' => $request->shop_name,
                'shop_address' => $request->shop_address,
                'NID_number' => $request->NID_number,

                'profile_image' => $profilePath,
                'trade_license_front' => $frontPath,
                'trade_license_back' => $backPath,

                'commission_rate' => $request->commission_rate ?? 0,
                'payment_method' => $request->payment_method,
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'bank_account_number' => $request->bank_account_number,
            ]);

            event(new Registered($user));

            $this->notifyAdminAboutVendorRegistration($user);
        });

        return redirect()
            ->route('vendor.registration.success')
            ->with('success', 'Your vendor account has been submitted and is pending admin approval.');
    }

    /**
     * Notify admin about new vendor registration
     */
    protected function notifyAdminAboutVendorRegistration(User $vendor): void
    {
        // You can implement email notification or database notification
        // Example: Notification::send($admins, new NewVendorRegistration($vendor));

        // For now, let's create a simple database notification
        // $admins = User::role('admin')->get();
        // foreach ($admins as $admin) {
        //     $admin->notify(new \App\Notifications\NewVendorRegistration($vendor));
        // }
    }
}
