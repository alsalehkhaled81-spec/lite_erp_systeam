<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public $email;
    public $statusMessage = '';

    public function sendResetLink()
    {
        $this->validate([
            'email' => 'required|email|exists:users,email',
        ],[
            'email.exists' => 'هذا البريد الإلكتروني غير مسجل لدينا.',
        ]);

        // إرسال رابط الاستعادة باستخدام Laravel Password Broker
        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->statusMessage = 'تم إرسال رابط استعادة كلمة المرور إلى بريدك الإلكتروني بنجاح!';
            $this->email = ''; // تفريغ الحقل
        } else {
            $this->addError('email', 'حدث خطأ أثناء إرسال الرابط، يرجى المحاولة لاحقاً.');
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')->layout('components.layouts.app');
    }
}
