<?php

namespace App\Livewire\Admin\Vendor;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\VendorDetail;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

class VendorIndex extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    //For create/edit modal
    public $showModal = false;
    public $modalTitle = 'Add New Vendor';
    public $vendorId = null;

    //Form Fields
    public $name;
    public $email;
    public $phone;
    public $password;
    public $password_confirmation;
    public $status = 1; // Default to active
    public $shop_name;
    public $shop_address;
    public $NID_number;
    public $trade_license_number;
    public $payment_method;
    public $bank_name;
    public $account_holder_name;
    public $bank_account_number;
    public $selectRole = 'vendor';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->vendorId,
            'phone' => 'nullable|string|max:20',

            'password' => $this->vendorId
                ? 'nullable|string|min:6|confirmed'
                : 'required|string|min:6|confirmed',

            'shop_name' => 'required|string|max:255',
            'shop_address' => 'required|string|max:500',

            // 🔥 FIXED UNIQUE RULES
            'NID_number' => 'required|string|max:50|unique:vendor_details,NID_number,' . optional(
                VendorDetail::where('user_id', $this->vendorId)->first()
            )->id,

            'trade_license_number' => 'required|string|max:50|unique:vendor_details,trade_license_number,' . optional(
                VendorDetail::where('user_id', $this->vendorId)->first()
            )->id,

            'payment_method' => 'nullable|string|in:bank,bkash,nagad,rocket,cash',
            'bank_name' => 'nullable|required_if:payment_method,bank|string|max:255',
            'bank_account_number' => 'nullable|required_if:payment_method,bank|string|max:50',
            'account_holder_name' => 'nullable|required_if:payment_method,bank|string|max:255',

            'status' => 'boolean',
        ];
    }



    protected $validationAttributes = [
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'password' => 'Password',
        'password_confirmation' => 'Confirm Password',
        'shop_name' => 'Shop Name',
        'shop_address' => 'Shop Address',
        'NID_number' => 'NID Number',
        'trade_license_number' => 'Trade License Number',
        'payment_method' => 'Payment Method',
        'bank_name' => 'Bank Name',
        'account_holder_name' => 'Account Holder Name',
        'bank_account_number' => 'Bank Account Number',
    ];


    public function render()
    {
        $vendors = User::role('vendor')
            ->with(['vendorDetails', 'products'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhereHas('vendorDetails', function ($q) {
                            $q->where('shop_name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_vendor_approved', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.vendor.vendor-index', [
            'vendors' => $vendors,
            'totalVendors' => User::role('vendor')->count(),
            'activeVendors' => User::role('vendor')->where('is_vendor_approved', true)->count(),
            'inactiveVendors' => User::role('vendor')->where('is_vendor_approved', false)->count(),
        ])->layout('livewire.admin.layouts.app');
    }

    public function openModal($id = null)
    {
        $this->resetForm();
        $this->resetErrorBag();

        if ($id) {
            $this->vendorId = $id;
            $this->modalTitle = 'Edit Vendor';
            $vendor = User::with('vendorDetails')->findOrFail($id);

            $this->name = $vendor->name;
            $this->email = $vendor->email;
            $this->phone = $vendor->phone;
            $this->status = $vendor->status;

            if ($vendor->vendorDetails) {
                $this->shop_name = $vendor->vendorDetails->shop_name;
                $this->shop_address = $vendor->vendorDetails->shop_address;

                $this->NID_number = $vendor->vendorDetails->NID_number;
                $this->trade_license_number = $vendor->vendorDetails->trade_license_number;

                $this->payment_method = $vendor->vendorDetails->payment_method;
                $this->bank_name = $vendor->vendorDetails->bank_name;
                $this->account_holder_name = $vendor->vendorDetails->account_holder_name;
                $this->bank_account_number = $vendor->vendorDetails->bank_account_number;
            }
        } else {
            $this->modalTitle = 'Add New Vendor';
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'vendorId',
            'name',
            'email',
            'phone',
            'status',
            'shop_name',
            'shop_address',
            'NID_number',
            'trade_license_number',
            'payment_method',
            'bank_name',
            'account_holder_name',
            'bank_account_number',
            'password',
            'password_confirmation',
        ]);
    }

    public function saveVendor()
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated, &$user) {

            if ($this->vendorId) {
                $user = User::findOrFail($this->vendorId);
                $user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'status' => $this->status,
                    'password' => $this->password
                        ? bcrypt($this->password)
                        : $user->password,
                ]);
            } else {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'password' => bcrypt($validated['password']),
                    'status' => false,              //Inactive
                    'is_vendor_approved' => false,  //Pending Approval
                ]);

                $user->syncRoles('vendor');
            }

            $user->vendorDetails()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'shop_name' => $validated['shop_name'],
                    'shop_address' => $validated['shop_address'],
                    'NID_number' => $validated['NID_number'],
                    'trade_license_number' => $validated['trade_license_number'],
                    'payment_method' => $this->payment_method,
                    'bank_name' => $this->bank_name,
                    'bank_account_number' => $this->bank_account_number,
                    'account_holder_name' => $this->account_holder_name,
                ]
            );
        });


        $this->closeModal();
        session()->flash(
            'message',
            $this->vendorId
                ? 'Vendor updated successfully!'
                : 'Vendor created successfully!'
        );
    }



    public function toggleStatus($id)
    {
        $vendor = User::findOrFail($id);
        $vendor->update(['status' => !$vendor->status]);

        $status = $vendor->status ? 'activated' : 'deactivated';
        $this->dispatch('show-toast', type: 'success', message: "Vendor {$status} successfully!");
    }

    public function toggleApproval($id)
    {
        $vendor = User::role('vendor')->findOrFail($id);

        if ($vendor->is_vendor_approved) {
            // Move to Pending
            $vendor->update([
                'is_vendor_approved' => false,
                'status' => false,
            ]);

            $message = 'Vendor moved to pending. Access revoked.';
        } else {
            // Approve Vendor
            $vendor->update([
                'is_vendor_approved' => true,
                'status' => true,
            ]);

            $message = 'Vendor approved successfully!';
        }

        $this->dispatch('show-toast', type: 'success', message: $message);
    }




    public function deleteVendor($id)
    {
        $vendor = User::findOrFail($id);

        // Check if vendor has products
        if ($vendor->products()->count() > 0) {
            $this->dispatch('show-toast', type: 'error', message: 'Cannot delete vendor with existing products!');
            return;
        }

        // Delete vendor logo if exists
        if ($vendor->vendorDetail && $vendor->vendorDetail->shop_logo) {
            $logoPath = public_path('storage/' . $vendor->vendorDetail->shop_logo);
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }
        }

        $vendor->delete();
        $this->dispatch('show-toast', type: 'success', message: 'Vendor deleted successfully!');
    }

    public function exportVendors()
    {
        $this->dispatch('show-toast', type: 'info', message: 'Export feature coming soon!');
    }

    public function updatedPerPage($value)
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
