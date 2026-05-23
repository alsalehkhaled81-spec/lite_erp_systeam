<div>
    <div style="text-align: center; margin-bottom: 2rem;">
        <h2>{{ __('filament.auth.reset_password_title') }}</h2>
        <p class="subtitle">{{ __('filament.auth.reset_password_subtitle') }}</p>
    </div>

    <form wire:submit.prevent="resetPassword">
        <input type="hidden" wire:model="token">

        <div class="form-group">
            <label>{{ __('filament.auth.email') }}</label>
            <input type="email" wire:model="email" readonly style="opacity: 0.6; cursor: not-allowed;">
            @error('email') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>{{ __('filament.auth.new_password') }}</label>
            <input type="password" wire:model="password" placeholder="••••••••">
            @error('password') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>{{ __('filament.auth.password_confirmation') }}</label>
            <input type="password" wire:model="password_confirmation" placeholder="••••••••">
        </div>

        <button type="submit" class="btn-primary btn-success">
            {{ __('filament.auth.save_password') }}
        </button>
    </form>
</div>
