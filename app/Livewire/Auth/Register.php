<?php

namespace App\Livewire\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public $name;

    public $email;

    public $password;

    public $password_confirmation;

    public $role_id;

    public function register()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $role = Role::where('name', 'employee')->first();

        if (! $role) {
            $this->addError('email', 'حدث خطأ في النظام. دور الموظف غير موجود.');
            return;
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role_id' => $role->id,
            'is_approved' => false,
        ]);

        Auth::login($user);

        return redirect()->route('job.apply');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('components.layouts.app');
    }
}
