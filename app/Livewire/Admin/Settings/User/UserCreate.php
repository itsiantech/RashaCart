<?php

namespace App\Livewire\Admin\Settings\User;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserCreate extends Component
{

    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $selectedRole;
    public $roles;

    public function mount(){
       $this->roles = Role::all();
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => 'required|numeric|digits:11',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'selectedRole' => ['required', 'string'],
        ];
    }

    public function save()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $user->assignRole($this->selectedRole);

        $this->dispatch('toast', 
            message: 'User created successfully!', 
            type: 'success'
        );        
        
        $this->reset(['name', 'phone', 'email', 'password', 'password_confirmation', 'selectedRole']);
        return $this->redirect(route('admin.user.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.settings.user.user-create')->layout('livewire.admin.layouts.app');
    }
}
