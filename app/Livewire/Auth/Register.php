<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
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
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => \Illuminate\Support\Facades\Hash::make($this->password),
            'role_id' => $this->role_id,
        ]);

        \Illuminate\Support\Facades\Auth::login($user);

        return $this->redirectBasedOnRole($user->role->name);
    }

    private function redirectBasedOnRole($roleName)
    {
        return match ($roleName) {
            'super_admin' => redirect('/admin'),
            'hr_manager' => redirect('/hr'),
            'project_manager' => redirect('/pm'),
            'accountant' => redirect('/accountant'),
            'employee' => redirect()->route('job.apply'), // توجيهه لصفحة التوظيف
            default => redirect('/'),
        };
    }


    public function render()
    {
        // جلب الأدوار (باستثناء المدير العام، ويمكنك استثناء أي دور آخر تريده)
        $roles = Role::where('name', '!=', 'super_admin')->get();

        return view('livewire.auth.register', ['roles' => $roles])
               ->layout('components.layouts.app');
    }
}
