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

        <div class="form-group">
            <label>{{ __('filament.fields.password') }}</label>
            <input type="password" wire:model="password" placeholder="••••••••">
            @error('password') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>{{ __('filament.fields.confirm_password') }}</label>
            <input type="password" wire:model="password_confirmation" placeholder="••••••••">
        </div>

        <div class="form-group">
            <label>{{ __('filament.fields.role') }}</label>
            <select wire:model="role_id">
                <option value="">-- {{ __('filament.auth.choose_role') }} --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->description ?? $role->name }}</option>
                @endforeach
            </select>
            @error('role_id') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn-primary btn-success">
            {{ __('filament.auth.register_account') }}
        </button>
    </form>

    <div class="footer-text">
        <p>{{ __('filament.auth.already_have_account') }} <a href="{{ route('login') }}">{{ __('filament.auth.login') }}</a></p>
    </div>
</div>