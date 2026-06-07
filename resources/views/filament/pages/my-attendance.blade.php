<x-filament-panels::page>
    <div class="mb-6">
        <div class="flex gap-4 items-center">
            @if(!$todayRecord || !$todayRecord->check_in)
                <x-filament::button color="success" icon="heroicon-o-arrow-right-circle" wire:click="checkIn">
                    {{ __('filament.attendance.check_in') }}
                </x-filament::button>
            @elseif(!$todayRecord->check_out)
                <x-filament::button color="danger" icon="heroicon-o-arrow-left-circle" wire:click="checkOut">
                    {{ __('filament.attendance.check_out') }}
                </x-filament::button>
            @else
                <x-filament::badge color="success">
                    {{ __('filament.attendance.day_completed') }}
                </x-filament::badge>
            @endif
        </div>
    </div>

    @if($todayRecord)
        <x-filament::section heading="{{ __('filament.attendance.today_status') }}">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('filament.fields.check_in') }}</p>
                    <p class="font-semibold">{{ $todayRecord->check_in ? $todayRecord->check_in->format('H:i') : '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('filament.fields.check_out') }}</p>
                    <p class="font-semibold">{{ $todayRecord->check_out ? $todayRecord->check_out->format('H:i') : '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('filament.fields.hours_worked') }}</p>
                    <p class="font-semibold">{{ $todayRecord->hours_worked }} {{ __('filament.fields.hours_unit') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('filament.columns.status') }}</p>
                    <x-filament::badge color="{{ $todayRecord->status === 'present' ? 'success' : ($todayRecord->status === 'late' ? 'warning' : 'danger') }}">
                        {{ __('filament.attendance.' . $todayRecord->status) }}
                    </x-filament::badge>
                </div>
            </div>
        </x-filament::section>
    @endif

    <x-filament::section heading="{{ __('filament.attendance.monthly_records') }}" class="mt-4">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b dark:border-gray-700">
                    <th class="text-start py-2 px-3">{{ __('filament.fields.date') }}</th>
                    <th class="text-start py-2 px-3">{{ __('filament.fields.check_in') }}</th>
                    <th class="text-start py-2 px-3">{{ __('filament.fields.check_out') }}</th>
                    <th class="text-start py-2 px-3">{{ __('filament.fields.hours_worked') }}</th>
                    <th class="text-start py-2 px-3">{{ __('filament.columns.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyRecords as $record)
                    <tr class="border-b dark:border-gray-700/50">
                        <td class="py-2 px-3">{{ $record->date->format('Y-m-d') }}</td>
                        <td class="py-2 px-3">{{ $record->check_in ? $record->check_in->format('H:i') : '-' }}</td>
                        <td class="py-2 px-3">{{ $record->check_out ? $record->check_out->format('H:i') : '-' }}</td>
                        <td class="py-2 px-3">{{ $record->hours_worked }} {{ __('filament.fields.hours_unit') }}</td>
                        <td class="py-2 px-3">
                            <x-filament::badge color="{{ $record->status === 'present' ? 'success' : ($record->status === 'late' ? 'warning' : 'danger') }}">
                                {{ __('filament.attendance.' . $record->status) }}
                            </x-filament::badge>
                        </td>
                    </tr>
                @endforeach
                @if($monthlyRecords->isEmpty())
                    <tr><td colspan="5" class="py-4 text-center text-gray-400">{{ __('filament.attendance.no_records') }}</td></tr>
                @endif
            </tbody>
        </table>
    </x-filament::section>
</x-filament-panels::page>
