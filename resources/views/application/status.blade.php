<x-layouts.app>
    <div>
        <div style="text-align: center; margin-bottom: 1.5rem;">
            @if($employee->status === 'pending')
                <div style="width: 64px; height: 64px; margin: 0 auto 1rem; background: rgba(234,179,8,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 32px; height: 32px; color: #fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2>{{ __('filament.application.under_review_title') }}</h2>
                @if($employee->vacancy)
                    <p class="subtitle" style="margin-bottom: 0.25rem;">{{ __('filament.auth.applied_for') }}: <strong>{{ $employee->vacancy->title }}</strong></p>
                @endif
                <p class="subtitle">{{ __('filament.application.under_review_desc') }}</p>
            @elseif($employee->status === 'rejected')
                <div style="width: 64px; height: 64px; margin: 0 auto 1rem; background: rgba(239,68,68,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 32px; height: 32px; color: #f87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2>{{ __('filament.application.rejected_title') }}</h2>
                @if($employee->rejection_reason)
                    <div style="margin-top: 1rem; padding: 1rem; background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); border-radius: 12px; text-align: start;">
                        <p style="color: #fca5a5; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.4rem;">{{ __('filament.application.rejection_reason_label') }}</p>
                        <p style="color: #fca5a5; font-size: 0.85rem;">{{ $employee->rejection_reason }}</p>
                    </div>
                @endif
                <p class="subtitle" style="margin-top: 1rem;">{{ __('filament.application.rejected_desc') }}</p>
            @endif
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 16px rgba(239,68,68,0.25);">{{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Logout' }}</button>
        </form>
    </div>
</x-layouts.app>
