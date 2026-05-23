<div>
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

        <div class="form-group">
            <label>{{ __('filament.fields.password') }}</label>
            <input type="password" wire:model="password" placeholder="••••••••">
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