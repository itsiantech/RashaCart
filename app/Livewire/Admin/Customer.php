<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Customer extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $customers = User::role('customer')
            ->with(['roles', 'orders'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.customer', compact('customers'))
            ->layout('livewire.admin.layouts.app');
    }

    public function toggleStatus($userId)
    {
        abort_unless(auth()->user()->can('user.update'), 403);

        $user = User::findOrFail($userId);
        $user->status = $user->status === 1 ? 0 : 1;
        $user->save();

        $statusText = $user->status === 1 ? 'active' : 'inactive';
        $this->dispatch('success', message: 'User status '.ucfirst($statusText), type: 'success');
    }

    public function deleteadmin($id)
    {
        try {
            $admin = User::findOrFail($id);

            if ($admin->id === auth()->id()) {
                $this->dispatch('toast', message: 'You cannot delete yourself!', type: 'error');

                return;
            }

            if ($admin->hasRole('admin')) {
                $this->dispatch('toast', message: 'You cannot delete a Super Admin!', type: 'error');

                return;
            }
            $admin->delete();
            $this->dispatch('toast', message: 'Admin deleted successfully.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Failed to delete admin: '.$e->getMessage(), type: 'error');
        }
    }
}
