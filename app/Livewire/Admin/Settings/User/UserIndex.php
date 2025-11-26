<?php

namespace App\Livewire\Admin\Settings\User;

use Livewire\Component;
use Spatie\Permission\Models\Role;

use App\Models\User;
use Livewire\WithPagination;
class UserIndex extends Component
{
     use WithPagination;

    public $search = '';

    public function render()
    {
        $admins = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'user'))
            ->with('roles')
            ->when($this->search, fn($query) => 
                $query->where('name', 'like', '%' . $this->search . '%')
            )
            ->latest()
            ->paginate(10);

        return view('livewire.admin.settings.user.user-index', compact('admins'))
            ->layout('livewire.admin.layouts.app');
    }

    public function toggleStatus($userId)
    {
        abort_unless(auth()->user()->can('user.update'), 403);

        $user = User::findOrFail($userId);        
        $user->status = $user->status === 1 ? 0 : 1;
        $user->save();

        $statusText = $user->status === 1 ? 'active' : 'inactive';
        $this->dispatch('success', message: 'User status ' . ucfirst($statusText), type: 'success');
    }


    public function deleteadmin($id)
    {
        try {
            $admin = User::findOrFail($id);
            
            if ($admin->id === auth()->id()) {
                $this->dispatch('toast', message: 'You cannot delete yourself!', type: 'error');
                return;
            }

            if ($admin->hasRole('super-admin')) {
                $this->dispatch('success', message: 'You cannot delete a Super Admin!', type: 'error');
                return;
            }
            $admin->delete();
            $this->dispatch('success', message: 'Admin deleted successfully.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to delete admin: ' . $e->getMessage(), type: 'error');
        }
    }
  
}
