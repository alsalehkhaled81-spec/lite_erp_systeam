<div>
    <div style="text-align: center; margin-bottom: 2rem;">
        <h2>{{ __('filament.auth.create_employee_account') }}</h2>
        <p class="subtitle">{{ __('filament.auth.join_team') }}</p>
    </div>

    <form wire:submit.prevent="register">
        <div class="form-group">
            <label>{{ __('filament.fields.full_name') }}</label>
            <input type="text" wire:model="name" placeholder="{{ __('filament.auth.enter_full_name') }}">
            @error('name') <span class="error-text">{{ $message }}</span> @enderror
        </div>

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

        <div class="form-group" x-data="{ show: false }">
            <label>{{ __('filament.fields.confirm_new_password') }}</label>
            <div style="position: relative; display: flex; align-items: center;">
                <input :type="show ? 'text' : 'password'" wire:model="password_confirmation" placeholder="••••••••" style="width: 100%; padding-left: 40px;">
                <button type="button" @click="show = !show" style="position: absolute; left: 10px; background: none; border: none; color: var(--link-color); cursor: pointer; padding: 0;">
                    <x-heroicon-o-eye x-show="!show" style="width: 20px; height: 20px;" />
                    <x-heroicon-o-eye-slash x-show="show" style="width: 20px; height: 20px; display: none;" />
                </button>
            </div>
            @error('password_confirmation') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn-primary btn-success">
            {{ __('filament.auth.register_account') }}
        </button>
    </form>

    <div class="footer-text">
        <p>{{ __('filament.auth.already_have_account') }} <a href="{{ route('login') }}">{{ __('filament.auth.login') }}</a></p>
    </div>
</div>
