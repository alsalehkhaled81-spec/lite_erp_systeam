<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('filament.widgets.personal_calendar') }}
        </x-slot>
        <x-slot name="headerEnd">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400" dir="rtl">
                {{ $month }} {{ $year }}
            </span>
        </x-slot>

        <div>
            <div class="grid grid-cols-7 gap-1 mb-2">
                @foreach (['أحد', 'إثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة', 'سبت'] as $dayName)
                    <div class="py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">
                        {{ $dayName }}
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 gap-1">
                @for ($i = 0; $i < $firstDayOfWeek; $i++)
                    <div class="min-h-[80px] rounded-lg bg-gray-50 dark:bg-gray-800/50"></div>
                @endfor

                @for ($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        $dayEvents = $events->get($dateStr, collect());
                        $isToday = ($dateStr === $today);
                    @endphp

                    <div class="min-h-[80px] rounded-lg border p-1.5 transition-colors {{ $isToday
                        ? 'border-primary-500 ring-2 ring-primary-500/30 bg-primary-50/50 dark:bg-primary-900/20 dark:border-primary-400'
                        : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900'
                    }}">
                        <div class="mb-1 text-right text-sm font-medium {{ $isToday
                            ? 'text-primary-600 dark:text-primary-400'
                            : 'text-gray-900 dark:text-white'
                        }}">
                            {{ $day }}
                        </div>

                        <div class="flex flex-col gap-0.5 overflow-hidden">
                            @foreach ($dayEvents->take(3) as $event)
                                <span class="inline-flex truncate rounded px-1 py-0.5 text-[10px] leading-tight font-medium {{ $event['type'] === 'task'
                                    ? ($event['color'] === 'gray'
                                        ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                                        : ($event['color'] === 'yellow'
                                            ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300'
                                            : ($event['color'] === 'blue'
                                                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300'
                                                : 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300'
                                            )))
                                    : 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300'
                                }}">
                                    {{ $event['title'] }}
                                </span>
                            @endforeach

                            @if ($dayEvents->count() > 3)
                                <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                    +{{ $dayEvents->count() - 3 }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-center gap-4 border-t border-gray-200 pt-3 dark:border-gray-700">
                <div class="flex items-center gap-1.5">
                    <span class="inline-block h-3 w-3 rounded bg-gray-400 dark:bg-gray-600"></span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('filament.widgets.tasks') }} - Todo</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="inline-block h-3 w-3 rounded bg-yellow-500"></span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('filament.widgets.tasks') }} - In Progress</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="inline-block h-3 w-3 rounded bg-blue-500"></span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('filament.widgets.tasks') }} - Review</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="inline-block h-3 w-3 rounded bg-green-500"></span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('filament.widgets.tasks') }} - Done</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="inline-block h-3 w-3 rounded bg-purple-500"></span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('filament.widgets.leaves') }}</span>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>