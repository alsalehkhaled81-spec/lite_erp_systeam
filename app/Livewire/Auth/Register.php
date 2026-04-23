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

        // 1. إنشاء المستخدم
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role_id' => $this->role_id,
        ]);

        // 2. إنشاء ملف موظف مرتبط به (هام جداً لكي يعمل النظام معه)
        Employee::create([
            'user_id' => $user->id,
            'status' => 'active',
            'hire_date' => now(),
        ]);

        // 3. تسجيل الدخول
        Auth::login($user);

        // 4. التوجيه بناءً على الدور
        return $this->redirectBasedOnRole($user->role->name);
    }

    private function redirectBasedOnRole($roleName)
    {
        return match ($roleName) {
            'hr_manager' => redirect('/hr'),
            'project_manager' => redirect('/pm'),
            'accountant' => redirect('/accountant'),
            'employee' => redirect('/employee'),
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
