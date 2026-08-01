<?php

namespace App\Livewire\Admin\Settings\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $deleteUserId = null;

    protected $listeners = ['deleteConfirmed'];

    public function confirmDelete($userId)
    {
        $this->deleteUserId = $userId;
        $this->dispatch('success', message: 'confirmDelete called with ID: '.$userId);
        $this->js("window.dispatchEvent(new CustomEvent('showDeleteConfirmation'))");
    }

    public function deleteConfirmed()
    {
        if (! $this->deleteUserId) {
            return;
        }

        $user = User::find($this->deleteUserId);

        if (! $user) {
            $this->dispatch('error', message: 'User not found.');
            $this->deleteUserId = null;

            return;
        }

        if ($user->id === auth()->id()) {
            $this->dispatch('error', message: 'You cannot delete yourself.');
            $this->deleteUserId = null;

            return;
        }

        // if ($user->hasRole('admin')) {
        //     $this->dispatch('error', message: 'Admin account cannot be deleted.');
        //     $this->deleteUserId = null;
        //     return;
        // }

        $user->delete();
        $this->deleteUserId = null;
        $this->dispatch('success', message: 'Staff deleted successfully.');
    }

    public function toggleStatus($userId)
    {
        $user = User::find($userId);
        if ($user && ! $user->hasRole('admin')) {
            $user->update(['status' => ! $user->status]);
            $this->dispatch('success', message: 'Status updated.');
        }
    }

    public function render()
    {
        $admins = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'vendor');
        })
            ->with('roles')
            ->when(
                $this->search,
                fn ($query) => $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                })
            )
            ->latest()
            ->paginate(10);

        return view('livewire.admin.settings.user.user-index', compact('admins'))
            ->layout('livewire.admin.layouts.app');
    }
}
