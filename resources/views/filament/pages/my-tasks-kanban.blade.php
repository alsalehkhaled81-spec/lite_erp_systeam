<x-filament-panels::page>
    <div class="flex gap-5 overflow-x-auto pb-6 scroll-smooth" style="scrollbar-width: thin;">
        @foreach($statuses as $status)
            @php
                $statusColors = [
                    'todo'        => ['from' => '#6366f1', 'to' => '#818cf8', 'bg' => 'rgba(99,102,241,0.05)', 'border' => 'rgba(99,102,241,0.12)'],
                    'in_progress' => ['from' => '#f59e0b', 'to' => '#fbbf24', 'bg' => 'rgba(245,158,11,0.05)', 'border' => 'rgba(245,158,11,0.12)'],
                    'review'      => ['from' => '#3b82f6', 'to' => '#60a5fa', 'bg' => 'rgba(59,130,246,0.05)', 'border' => 'rgba(59,130,246,0.12)'],
                    'done'        => ['from' => '#10b981', 'to' => '#34d399', 'bg' => 'rgba(16,185,129,0.05)', 'border' => 'rgba(16,185,129,0.12)'],
                ];
                $colors = $statusColors[$status['id']] ?? $statusColors['todo'];
            @endphp
            <div class="flex-shrink-0 w-80 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-lg"
                 style="background: {{ $colors['bg'] }}; border: 1px solid {{ $colors['border'] }};">

                <div class="px-5 py-4 flex items-center justify-between"
                     style="background: linear-gradient(135deg, {{ $colors['from'] }}, {{ $colors['to'] }});">
                    <h3 class="font-bold text-base text-white tracking-wide">{{ $status['title'] }}</h3>
                    <span class="flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold"
                          style="background: rgba(255,255,255,0.25); color: #fff;">
                        {{ $status['records']->count() }}
                    </span>
                </div>

                <div class="space-y-3 p-4 min-h-[120px]">
                    @foreach($status['records'] as $task)
                        <div class="group rounded-xl p-4 transition-all duration-200 cursor-pointer hover:-translate-y-0.5 hover:shadow-md
                                    bg-white/80 dark:bg-gray-800/60 border border-gray-100/80 dark:border-gray-700/40
                                    backdrop-blur-sm">
                            <div class="font-semibold text-sm text-gray-800 dark:text-gray-100 mb-1.5 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ $task->title }}
                            </div>
                            @if($task->project?->name)
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                                    <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                    </svg>
                                    {{ $task->project->name }}
                                </div>
                            @endif
                            @if($task->due_date)
                                @php
                                    $dueDate = \Carbon\Carbon::parse($task->due_date);
                                @endphp
                                <div class="flex items-center gap-1.5 text-xs mt-2 px-2.5 py-1 rounded-full w-fit
                                            {{ $dueDate->isPast() ? 'bg-red-50/80 text-red-600 dark:bg-red-900/20 dark:text-red-400' : 'bg-gray-50/80 text-gray-500 dark:bg-gray-700/30 dark:text-gray-400' }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $dueDate->format('Y-m-d') }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                    @if($status['records']->isEmpty())
                        <div class="flex flex-col items-center justify-center py-8 text-gray-400/60 dark:text-gray-500/60">
                            <svg class="w-10 h-10 mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span class="text-xs font-medium">{{ __('filament.kanban.no_tasks') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
