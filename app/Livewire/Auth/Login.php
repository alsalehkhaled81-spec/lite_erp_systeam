<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email;

    public $password;

    public $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            $user = Auth::user();
            $roleName = $user->role->name ?? null;

            // منع دخول الحسابات غير المعتمدة عدا دور الموظف (يسمح له برؤية حالة طلبه فقط)
            if (! $user->is_approved && $roleName !== 'employee') {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                $this->addError('email', 'حسابك في انتظار موافقة الإدارة. يرجى التواصل مع المدير.');

                return;
            }

            $defaultPath = match ($roleName) {
                'super_admin' => '/admin',
                'hr_manager' => '/hr',
                'project_manager' => '/pm',
                'accountant' => '/accountant',
                'employee' => $user->is_approved ? '/employee' : '/apply',
                default => null,
            };

            if (! $defaultPath) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                $this->addError('email', 'حسابك لا يملك صلاحية للدخول إلى النظام.');

                return;
            }

            // 3. التوجيه الذكي:
            // سيتم توجيهه للرابط الذي كان يقصده قبل الدخول (إن وُجد)، أو للمسار الافتراضي للوحته
            return redirect($defaultPath);
        }

        if (Auth::guard('client')->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $client = Auth::guard('client')->user();

            if (! $client->is_active) {
                Auth::guard('client')->logout();
                session()->invalidate();
                session()->regenerateToken();
                $this->addError('email', 'حسابك غير مفعّل. يرجى التواصل مع الإدارة.');

                return;
            }

            session()->regenerate();

            return redirect('/client');
        }

        $this->addError('email', 'بيانات الدخول غير صحيحة.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.app');
    }
}
