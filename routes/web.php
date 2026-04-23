<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;



Route::redirect('/', '/login');

Route::get('/forgot-password', ForgotPassword::class)->name('password.request')->middleware('guest');

// مسار إعادة تعيين كلمة المرور (يحتوي على التوكن)
Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset')->middleware('guest');

// مسار تسجيل الدخول المركزي (يُسمى login ليستخدمه النظام للتحويل التلقائي)
Route::get('/login', Login::class)->name('login')->middleware('guest');

// مسار إنشاء الحساب
Route::get('/register', Register::class)->name('register')->middleware('guest');

// مسار تسجيل الخروج
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');
