<div>
    @if(session('success'))
        <div class="success-alert">
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom: 1.5rem;">
        <a href="{{ url('/') }}" class="link" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.95rem;">
            <svg xmlns="http://www.w3.org/2000/svg" style="width: 18px; height: 18px; transform: {{ app()->getLocale() === 'ar' ? 'rotate(180deg)' : 'none' }};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('filament.auth.back_to_home') }}
        </a>
    </div>

    <div style="text-align: center; margin-bottom: 2rem;">
        <h2>{{ __('filament.auth.login_title') }}</h2>
        <p class="subtitle">{{ __('filament.auth.login_subtitle') }}</p>
    </div>

    <form wire:submit.prevent="login">
        <div class="form-group">
            <label>{{ __('filament.fields.email') }}</label>
            <input type="email" wire:model="email" placeholder="example@company.com">
            @error('email') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" x-data="{ show: false }">
            <label>{{ __('filament.fields.password') }}</label>
            <div style="position: relative; display: flex; align-items: center;">
                <input :type="show ? 'text' : 'password'" wire:model="password" placeholder="••••••••" style="width: 100%; padding-left: 40px;">
                <button type="button" @click="show = !show" style="position: absolute; left: 10px; background: none; border: none; color: var(--link-color); cursor: pointer; padding: 0;">
                    <x-heroicon-o-eye x-show="!show" style="width: 20px; height: 20px;" />
                    <x-heroicon-o-eye-slash x-show="show" style="width: 20px; height: 20px; display: none;" />
                </button>
            </div>
            @error('password') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <div class="form-row">
            <label class="checkbox-label">
                <input type="checkbox" wire:model="remember">
                <span>{{ __('filament.auth.remember_me') }}</span>
            </label>
            <a href="{{ route('password.request') }}" class="link">{{ __('filament.auth.forgot_password') }}</a>
        </div>

        <button type="submit" class="btn-primary">
            {{ __('filament.auth.login_button') }}
        </button>
    </form>

    <div class="footer-text">
        <p>{{ __('filament.auth.no_account') }} <a href="{{ route('register') }}">{{ __('filament.auth.register_link') }}</a></p>
    </div>

</div>