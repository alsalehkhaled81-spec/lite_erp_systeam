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

        <div class="form-group" x-data="{ show: false }">
            <label>{{ __('filament.auth.new_password') }}</label>
            <div style="position: relative; display: flex; align-items: center;">
                <input :type="show ? 'text' : 'password'" wire:model="password" placeholder="••••••••" style="width: 100%; padding-left: 40px;">
                <button type="button" @click="show = !show" style="position: absolute; left: 10px; background: none; border: none; color: var(--link-color); cursor: pointer; padding: 0;">
                    <x-heroicon-o-eye x-show="!show" style="width: 20px; height: 20px;" />
                    <x-heroicon-o-eye-slash x-show="show" style="width: 20px; height: 20px; display: none;" />
                </button>
            </div>
            @error('password') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" x-data="{ show: false }">
            <label>{{ __('filament.auth.password_confirmation') }}</label>
            <div style="position: relative; display: flex; align-items: center;">
                <input :type="show ? 'text' : 'password'" wire:model="password_confirmation" placeholder="••••••••" style="width: 100%; padding-left: 40px;">
                <button type="button" @click="show = !show" style="position: absolute; left: 10px; background: none; border: none; color: var(--link-color); cursor: pointer; padding: 0;">
                    <x-heroicon-o-eye x-show="!show" style="width: 20px; height: 20px;" />
                    <x-heroicon-o-eye-slash x-show="show" style="width: 20px; height: 20px; display: none;" />
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary btn-success">
            {{ __('filament.auth.save_password') }}
        </button>
    </form>
</div>
