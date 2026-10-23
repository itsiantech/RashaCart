<?php

namespace App\Livewire\Frontend\Customer;

use App\Models\Order;
use App\Services\CustomerAuthSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Customer dashboard (single Livewire component, tabbed).
 * Route: /account/{tab?} — protected by auth + customer middleware.
 * All queries scoped to auth()->id() — users only see their own data.
 */
class CustomerDashboard extends Component
{
    use WithPagination;

    public string $tab = 'overview';

    // Status filter tabs for order history.
    public string $statusFilter = 'all';

    // Order tracking.
    public string $trackInvoice = '';
    public ?Order $trackedOrder = null;

    // Profile form.
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';

    // Password change.
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // Notification prefs.
    public bool $notify_sms = true;
    public bool $notify_email = true;

    public bool $needsPhone = false;

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
    ];

    public function mount(?string $tab = null): void
    {
        $allowed = ['overview', 'profile', 'track', 'orders', 'payments', 'invoices', 'settings'];
        if ($tab && in_array($tab, $allowed, true)) {
            $this->tab = $tab;
        } elseif (request()->routeIs('customer.dashboard') && request()->query('tab')) {
            $q = request()->query('tab');
            $this->tab = in_array($q, $allowed, true) ? $q : 'overview';
        }
        $user = Auth::user();
        $this->name = (string) ($user->name ?? '');
        $this->username = (string) ($user->username ?? '');
        $this->email = (string) ($user->email ?? '');
        $this->phone = (string) ($user->phone ?? '');
        $this->address = (string) ($user->address ?? '');
        $prefs = $user->notification_prefs ?? [];
        if (is_string($prefs)) {
            $prefs = json_decode($prefs, true) ?: [];
        }
        $this->notify_sms = (bool) ($prefs['sms'] ?? true);
        $this->notify_email = (bool) ($prefs['email'] ?? true);
        $this->needsPhone = session('customer_needs_phone', false) || empty($user->phone);

        // Preselect tracking input from ?invoice=
        if (request()->query('invoice')) {
            $this->trackInvoice = (string) request()->query('invoice');
            $this->trackOrder();
        }
    }

    public function setTab(string $tab): void
    {
        $allowed = ['overview', 'profile', 'track', 'orders', 'payments', 'invoices', 'settings'];
        if (in_array($tab, $allowed, true)) {
            $this->tab = $tab;
            $this->resetPage();
        }
    }

    public function setStatusFilter(string $filter): void
    {
        $this->statusFilter = $filter;
        $this->resetPage();
    }

    // ── Orders (scoped to owner) ──────────────────────────────────────

    protected function baseOrderQuery()
    {
        $q = Order::with(['items.product'])
            ->where('user_id', Auth::id())
            ->latest();
        if ($this->statusFilter !== 'all') {
            // "received" tab groups processing-ish statuses.
            if ($this->statusFilter === 'received') {
                $q->whereIn('status', ['received', 'processing', 'confirmed', 'shipped']);
            } elseif ($this->statusFilter === 'delivered') {
                $q->whereIn('status', ['delivered', 'completed']);
            } elseif ($this->statusFilter === 'on_hold') {
                $q->whereIn('status', ['on_hold', 'on hold', 'hold']);
            } else {
                $q->where('status', $this->statusFilter);
            }
        }
        return $q;
    }

    // ── Tracking ──────────────────────────────────────────────────────

    public function trackOrder(): void
    {
        $this->validate(['trackInvoice' => 'required|string|max:50']);
        $needle = trim($this->trackInvoice);
        $this->trackedOrder = Order::with(['items.product'])
            ->where('user_id', Auth::id())
            ->where(function ($q) use ($needle) {
                $q->where('invoice_no', $needle)->orWhere('id', $needle);
            })
            ->first();
        if (! $this->trackedOrder) {
            $this->dispatch('error', message: 'No order found with this ID. Check guest orders via Track Order page.');
        }
    }

    /** Open the Track tab pre-loaded with a specific order (Order History "Details" button). */
    public function openOrderDetails(string $ref): void
    {
        $this->trackInvoice = $ref;
        $this->tab = 'track';
        $this->trackOrder();
    }

    /** Visual timeline steps for an order status. */
    public static function timeline(Order $order): array
    {
        $steps = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
        $status = strtolower((string) $order->status);
        // Normalise known variants.
        $map = ['received' => 'processing', 'completed' => 'delivered', 'on_hold' => 'pending', 'on hold' => 'pending'];
        $status = $map[$status] ?? $status;
        $current = array_search($status, $steps, true);
        $cancelled = in_array($status, ['cancelled', 'returned'], true);
        return ['steps' => $steps, 'current' => $current === false ? 0 : $current, 'cancelled' => $cancelled];
    }

    /** Badge colour per status (shared by blade). */
    public static function badgeClass(string $status): string
    {
        return match (strtolower($status)) {
            'pending' => 'bg-warning text-dark',
            'on_hold', 'on hold' => 'bg-secondary',
            'received', 'processing', 'confirmed', 'shipped' => 'bg-info text-dark',
            'delivered', 'completed' => 'bg-success',
            'cancelled', 'returned' => 'bg-danger',
            default => 'bg-light text-dark',
        };
    }

    // ── Profile ───────────────────────────────────────────────────────

    public function saveProfile(): void
    {
        $user = Auth::user();
        $identifier = CustomerAuthSettings::identifier();
        $rules = [
            'name' => 'required|string|max:255',
            'username' => ['nullable', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9_.]+$/', Rule::unique('users', 'username')->ignore($user->id)],
            'address' => 'nullable|string|max:500',
        ];
        // Email/phone required-ness follows admin identifier setting.
        $rules['email'] = $identifier === 'phone'
            ? ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)]
            : ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)];
        $rules['phone'] = $identifier === 'email'
            ? ['nullable', 'string', 'max:20']
            : ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)];
        $this->validate($rules);

        $user->update([
            'name' => trim($this->name),
            'username' => $this->username ?: null,
            'email' => trim($this->email),
            'phone' => $this->phone ? preg_replace('/\D+/', '', $this->phone) : null,
            'address' => trim($this->address),
        ]);
        $this->needsPhone = empty($user->fresh()->phone);
        session()->forget('customer_needs_phone');
        $this->dispatch('success', message: 'Profile updated.');
    }

    public function savePhoneStep(): void
    {
        $this->validate(['phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore(Auth::id())]]);
        Auth::user()->update(['phone' => preg_replace('/\D+/', '', $this->phone)]);
        $this->needsPhone = false;
        session()->forget('customer_needs_phone');
        $this->dispatch('success', message: 'Phone number saved.');
    }

    public function changePassword(): void
    {
        $this->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);
        $user = Auth::user();
        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }
        $user->update(['password' => Hash::make($this->new_password)]);
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->dispatch('success', message: 'Password changed.');
    }

    public function saveNotifications(): void
    {
        Auth::user()->update(['notification_prefs' => ['sms' => $this->notify_sms, 'email' => $this->notify_email]]);
        $this->dispatch('success', message: 'Notification preferences saved.');
    }

    // ── Data for view ─────────────────────────────────────────────────

    public function getSummaryProperty(): array
    {
        $uid = Auth::id();
        $total = Order::where('user_id', $uid)->count();
        $pending = Order::where('user_id', $uid)->where('status', 'pending')->count();
        $delivered = Order::where('user_id', $uid)->whereIn('status', ['delivered', 'completed'])->count();
        $spent = Order::where('user_id', $uid)->whereNotIn('status', ['cancelled', 'returned'])->sum('total_amount');
        return compact('total', 'pending', 'delivered', 'spent');
    }

    public function render()
    {
        $uid = Auth::id();
        return view('livewire.frontend.customer.dashboard', [
            'summary' => $this->summary,
            'orders' => $this->tab === 'orders' ? $this->baseOrderQuery()->paginate(10) : null,
            'payments' => $this->tab === 'payments'
                ? Order::where('user_id', $uid)->latest()->paginate(10) : null,
            'invoices' => $this->tab === 'invoices'
                ? Order::where('user_id', $uid)->whereNotNull('invoice_no')->latest()->paginate(10) : null,
            'recentOrders' => $this->tab === 'overview'
                ? Order::with(['items'])->where('user_id', $uid)->latest()->limit(5)->get() : collect(),
            'identifier' => CustomerAuthSettings::identifier(),
        ])->layout('livewire.frontend.layouts.store');
    }
}
