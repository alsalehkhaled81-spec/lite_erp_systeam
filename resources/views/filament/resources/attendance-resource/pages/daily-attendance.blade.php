<x-filament-panels::page>
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">
                {{ __('filament.attendance.daily_attendance_title') }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $dateLabel }}</p>
        </div>

        <div class="w-full sm:w-auto">
            {{ $this->form }}
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <x-filament::section>
            <x-slot name="heading">
                <span class="text-gray-500 dark:text-gray-400 text-sm">{{ __('filament.attendance.total_employees') }}</span>
            </x-slot>
            <p class="text-3xl font-bold text-gray-800 dark:text-gray-200">{{ $totalEmployees }}</p>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                <span class="text-gray-500 dark:text-gray-400 text-sm">{{ __('filament.attendance.present_today') }}</span>
            </x-slot>
            <p class="text-3xl font-bold text-success-600">{{ $presentCount }}</p>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                <span class="text-gray-500 dark:text-gray-400 text-sm">{{ __('filament.attendance.absent_today') }}</span>
            </x-slot>
            <p class="text-3xl font-bold text-danger-600">{{ $absentCount }}</p>
        </x-filament::section>
    </div>

    <x-filament::section>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="text-start py-2 px-3">#</th>
                        <th class="text-start py-2 px-3">{{ __('filament.columns.employee_name') }}</th>
                        <th class="text-start py-2 px-3">{{ __('filament.columns.department') }}</th>
                        <th class="text-start py-2 px-3">{{ __('filament.columns.status') }}</th>
                        <th class="text-start py-2 px-3">{{ __('filament.fields.check_in_time') }}</th>
                        <th class="text-start py-2 px-3">{{ __('filament.fields.check_out_time') }}</th>
                        <th class="text-start py-2 px-3">{{ __('filament.fields.hours_worked') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $index => $row)
                        @php
                            $status = $row['status'];
                            $badgeColor = match($status) {
                                'present' => 'success',
                                'late' => 'warning',
                                'absent' => 'danger',
                                'half_day' => 'info',
                                'over_time' => 'primary',
                                default => 'gray',
                            };
                            $statusLabel = $status
                                ? __('filament.attendance.' . $status)
                                : __('filament.attendance.not_recorded');
                            $dutyLabel = $row['on_duty']
                                ? __('filament.attendance.on_duty')
                                : __('filament.attendance.not_on_duty');
                        @endphp
                        <tr class="border-b dark:border-gray-700/50">
                            <td class="py-2 px-3 text-gray-400">{{ $index + 1 }}</td>
                            <td class="py-2 px-3 font-medium text-gray-800 dark:text-gray-200">
                                {{ $row['employee']->user?->name ?? '—' }}
                            </td>
                            <td class="py-2 px-3 text-gray-600 dark:text-gray-400">
                                {{ $row['employee']->department?->name ?? '—' }}
                            </td>
                            <td class="py-2 px-3">
                                <div class="flex flex-wrap gap-1">
                                    <x-filament::badge color="{{ $row['on_duty'] ? 'success' : 'gray' }}">
                                        {{ $dutyLabel }}
                                    </x-filament::badge>
                                    <x-filament::badge color="{{ $badgeColor }}">
                                        {{ $statusLabel }}
                                    </x-filament::badge>
                                </div>
                            </td>
                            <td class="py-2 px-3">
                                {{ $row['attendance']?->check_in ? $row['attendance']->check_in->format('H:i') : '—' }}
                            </td>
                            <td class="py-2 px-3">
                                {{ $row['attendance']?->check_out ? $row['attendance']->check_out->format('H:i') : '—' }}
                            </td>
                            <td class="py-2 px-3">
                                @if($row['attendance']?->hours_worked)
                                    {{ $row['attendance']->hours_worked }} {{ __('filament.fields.hours_unit') }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    @if(empty($rows))
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-400">
                                {{ __('filament.attendance.no_records') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
