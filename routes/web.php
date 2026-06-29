<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\LandingController;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/vacancies', [LandingController::class, 'vacancies'])->name('vacancies.index');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session()->put('locale', $locale);
        cookie()->queue(cookie()->forever('filament_language_switch_locale', $locale));
    }
    return redirect()->back();
})->name('switch-language');

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

    Route::middleware('auth')->group(function () {

    Route::get('/apply', [JobApplicationController::class, 'index'])->name('job.apply');
    
    Route::post('/apply', [JobApplicationController::class, 'store'])->name('job.store');
});
