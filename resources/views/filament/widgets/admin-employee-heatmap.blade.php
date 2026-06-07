<x-filament-widgets::widget>
    <div class="fi-section rounded-xl bg-white dark:bg-gray-900 shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="fi-section-header flex items-center gap-2 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30">
                <x-heroicon-o-fire class="w-4 h-4 text-green-600 dark:text-green-400"/>
            </span>
            <h2 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ __('filament.widgets.employee_heatmap') }}</h2>
            <span class="text-xs text-gray-400 dark:text-gray-500 mr-auto">{{ __('filament.widgets.last_7_days') }}</span>
        </div>
        @if(count($heatmapData) > 0)
            <div class="p-6 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr>
                            <th class="text-left py-2 px-3 font-medium text-gray-500 dark:text-gray-400 w-40">{{ __('filament.widgets.employee') }}</th>
                            @foreach($days as $day)
                                <th class="text-center py-2 px-2 font-medium text-gray-500 dark:text-gray-400 text-xs">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($heatmapData as $row)
                            <tr class="border-t border-gray-50 dark:border-gray-800">
                                <td class="py-2 px-3 font-medium text-gray-800 dark:text-gray-200 truncate max-w-[140px]">{{ $row['name'] }}</td>
                                @foreach($row['data'] as $day => $count)
                                    @php
                                        $ratio = $maxCount > 0 ? $count / $maxCount : 0;
                                        if ($count === 0) {
                                            $bgClass = 'bg-gray-100 dark:bg-gray-800';
                                        } elseif ($ratio > 0.75) {
                                            $bgClass = 'bg-green-500 dark:bg-green-500';
                                        } elseif ($ratio > 0.5) {
                                            $bgClass = 'bg-green-300 dark:bg-green-700';
                                        } elseif ($ratio > 0.25) {
                                            $bgClass = 'bg-green-200 dark:bg-green-800';
                                        } else {
                                            $bgClass = 'bg-green-100 dark:bg-green-900';
                                        }
                                    @endphp
                                    <td class="text-center py-2 px-2">
                                        <div class="w-10 h-10 rounded-lg {{ $bgClass }} flex items-center justify-center mx-auto transition-all duration-200 hover:scale-110"
                                             title="{{ $day }}: {{ $count }} {{ __('filament.widgets.tasks') }}">
                                            <span class="text-xs font-bold {{ $count > 0 ? 'text-white' : 'text-gray-400 dark:text-gray-600' }}">{{ $count }}</span>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                <x-heroicon-o-fire class="w-8 h-8 mb-2 opacity-40"/>
                <p class="text-sm">{{ __('filament.widgets.no_heatmap_data') }}</p>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
