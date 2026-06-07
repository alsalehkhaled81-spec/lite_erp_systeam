<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('filament.widgets.my_completion_rate') }}
        </x-slot>

        <div class="flex flex-col items-center justify-center gap-6 py-2">

        <div class="relative flex items-center justify-center">
            <svg width="140" height="140" viewBox="0 0 140 140" class="transform -rotate-90">
                <circle
                    cx="70"
                    cy="70"
                    r="54"
                    stroke="currentColor"
                    stroke-width="10"
                    fill="none"
                    class="text-gray-200 dark:text-gray-700"
                />
                <circle
                    cx="70"
                    cy="70"
                    r="54"
                    stroke-width="10"
                    fill="none"
                    stroke-linecap="round"
                    stroke-dasharray="339.29"
                    stroke-dashoffset="339.29"
                    class="progress-ring-circle {{ $percentage > 70 ? 'text-green-500' : ($percentage >= 40 ? 'text-yellow-500' : 'text-red-500') }}"
                    style="stroke: currentColor; animation: progress-ring-fill 1.2s ease-out forwards; --target-offset: {{ 339.29 * (1 - $percentage / 100) }};"
                />
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $percentage }}%
                </span>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-4 text-sm">
            <div class="flex items-center gap-1.5">
                <span class="inline-block h-3 w-3 rounded-full bg-green-500"></span>
                <span class="text-gray-700 dark:text-gray-300">{{ __('filament.widgets.completed') }} ({{ $completed }})</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="inline-block h-3 w-3 rounded-full bg-yellow-500"></span>
                <span class="text-gray-700 dark:text-gray-300">{{ __('filament.widgets.in_progress') }} ({{ $inProgress }})</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="inline-block h-3 w-3 rounded-full bg-gray-400"></span>
                <span class="text-gray-700 dark:text-gray-300">{{ __('filament.widgets.pending') }} ({{ $pending }})</span>
            </div>
        </div>
        </div>
    </x-filament::section>

    <x-slot name="styles">
        <style>
            @keyframes progress-ring-fill {
                from {
                    stroke-dashoffset: 339.29;
                }
                to {
                    stroke-dashoffset: var(--target-offset);
                }
            }
        </style>
    </x-slot>
</x-filament-widgets::widget>
