<?php

namespace App\Livewire\Admin\Settings\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserEdit extends Component
{
    public User $user;

    public $name;

    public $email;

    public $phone;

    public $password;

    public $password_confirmation;

    public $selectedRole;

    public $roles;

    public function mount($id)
    {
        $this->roles = Role::all();
        $this->user = User::findOrFail($id);
        $this->name = $this->user->name;
        $this->phone = $this->user->phone;
        $this->email = $this->user->email;
        $this->selectedRole = $this->user->roles->first()?->name;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => 'required|numeric|digits_between:10,15',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'selectedRole' => ['required', 'string'],
        ];
    }

    public function update()
    {
        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'password' => $this->password ? Hash::make($this->password) : $this->user->password,
        ]);

        $this->user->syncRoles($this->selectedRole);

        $this->dispatch('toast',
            message: 'Admin updated successfully!',
            type: 'success'
        );

        return $this->redirect(route('admin.user.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.settings.user.user-edit')->layout('livewire.admin.layouts.app');
    }
}
