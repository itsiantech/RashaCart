<?php

namespace App\Livewire\Admin\Settings\RolePermission;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Livewire\WithPagination;

class RolePermissionIndex extends Component
{
   
    use WithPagination;
    public $search = '';
    public $showAddRoleForm = false;
    public $showEditRoleForm = false;

    public $RoleName = '';
    public $editingRoleId = null;

 

    public function render()
    {
        abort_unless(auth()->user()->can('settings.edit'), 403);
        abort_unless(auth()->user()->hasRole('super-admin|admin'), 403);
        
        $roles = Role::with('permissions')
            ->when($this->search, fn($query) => 
                $query->where('name', 'like', '%' . $this->search . '%')
            )
            ->latest()
            ->paginate(10);

        $permissions = Permission::all();
        return view('livewire.admin.settings.role-permission.role-permission-index',[
            'roles' => $roles,
            'permissions' => $permissions
        ])->layout('livewire.admin.layouts.app');
        
    }
   

    public function loadRoles()
    {
        $this->roles = Role::with('permissions')->latest()->get();
    }


    public function addRoleForm()
    {
        $this->reset(['RoleName', 'showEditRoleForm']);
        $this->showAddRoleForm = true;
    }

   
    public function roleStore()
    {
        abort_unless(auth()->user()->can('settings.edit'), 403);
        abort_unless(auth()->user()->hasRole('super-admin|admin'), 403);
        
        $validated = $this->validate([
            'RoleName' => 'required|string|min:3|unique:roles,name',
        ]);

        Role::create(['name' => $validated['RoleName']]);       
        $this->dispatch('success', message: 'Role Created!');
        $this->reset(['RoleName', 'showAddRoleForm']);
        $this->resetErrorBag();
       
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $this->editingRoleId = $role->id;
        $this->RoleName = $role->name;

        $this->showEditRoleForm = true;
        $this->showAddRoleForm = false;
        $this->resetErrorBag();
    }

    public function updateRole()
    {

        abort_unless(auth()->user()->can('settings.edit'), 403);
        abort_unless(auth()->user()->hasRole('super-admin|admin'), 403);
        
        $this->validate([
            'RoleName' => 'required|string|min:3|unique:roles,name,' . $this->editingRoleId,
        ]);

        $role = Role::findOrFail($this->editingRoleId);
        $role->name = $this->RoleName;
        $role->save();

       $this->dispatch('success', message: 'Role updated!');
        $this->reset(['RoleName', 'editingRoleId', 'showEditRoleForm']);
        $this->resetErrorBag();
    }

    public function deleteRole($id)
    {
        abort_unless(auth()->user()->can('settings.edit'), 403);
        abort_unless(auth()->user()->hasRole('super-admin|admin'), 403);
        
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            
            $this->dispatch('error', 
                message: 'Role deleted!',
                type: 'success'
            );
            
        } catch (\Exception $e) {
            $this->dispatch('error', 
                message: 'Failed to delete role: ' . $e->getMessage(),
                type: 'error'
            );
        }
    }
}
