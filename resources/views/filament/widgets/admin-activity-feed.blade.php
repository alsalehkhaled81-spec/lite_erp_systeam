<x-filament-widgets::widget>
    <div class="fi-section rounded-xl bg-white dark:bg-gray-900 shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="fi-section-header flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <x-heroicon-o-bolt class="w-4 h-4 text-blue-600 dark:text-blue-400"/>
                </span>
                <h2 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ __('filament.widgets.live_activity') }}</h2>
            </div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                {{ __('filament.widgets.live') }}
            </span>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-800 max-h-[420px] overflow-y-auto">
            @forelse($activities as $activity)
                <div class="flex items-start gap-3 px-6 py-3 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                    <span class="flex-shrink-0 flex items-center justify-center w-9 h-9 rounded-lg {{ $activity['color'] }}">
                        <x-dynamic-component :component="$activity['icon']" class="w-4 h-4"/>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $activity['title'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['description'] }}</p>
                    </div>
                    <span class="flex-shrink-0 text-[10px] text-gray-400 dark:text-gray-500 whitespace-nowrap">
                        {{ $activity['time']->diffForHumans() }}
                    </span>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                    <x-heroicon-o-clock class="w-8 h-8 mb-2 opacity-40"/>
                    <p class="text-sm">{{ __('filament.widgets.no_recent_activity') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-widgets::widget>
