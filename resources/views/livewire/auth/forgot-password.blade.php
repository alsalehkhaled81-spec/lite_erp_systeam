<div>
    <div style="text-align: center; margin-bottom: 2rem;">
        <h2>{{ __('filament.auth.forgot_password') }}</h2>
        <p class="subtitle">{{ __('filament.auth.forgot_password_desc') }}</p>
    </div>

    @if($statusMessage)
        <div class="success-alert">
            {{ $statusMessage }}
        </div>
    @endif

    <form wire:submit.prevent="sendResetLink">
        <div class="form-group">
            <label>{{ __('filament.fields.email') }}</label>
            <input type="email" wire:model="email" placeholder="example@company.com">
            @error('email') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn-primary">
            {{ __('filament.auth.send_reset_link') }}
        </button>
    </form>

    <div class="footer-text">
        <a href="{{ route('login') }}" class="link">{{ app()->getLocale() === 'ar' ? '←' : '→' }} {{ __('filament.auth.back_to_login') }}</a>
    </div>
</div>