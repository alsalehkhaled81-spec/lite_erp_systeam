<x-filament-panels::page>
    <div class="flex gap-5 overflow-x-auto pb-6 scroll-smooth" style="scrollbar-width: thin;">
        @foreach($statuses as $status)
            @php
                $statusColors = [
                    'todo'        => ['from' => '#6366f1', 'to' => '#818cf8', 'bg' => 'bg-indigo-50/50 dark:bg-indigo-900/10'],
                    'in_progress' => ['from' => '#f59e0b', 'to' => '#fbbf24', 'bg' => 'bg-amber-50/50 dark:bg-amber-900/10'],
                    'review'      => ['from' => '#3b82f6', 'to' => '#60a5fa', 'bg' => 'bg-blue-50/50 dark:bg-blue-900/10'],
                    'done'        => ['from' => '#10b981', 'to' => '#34d399', 'bg' => 'bg-emerald-50/50 dark:bg-emerald-900/10'],
                ];
                $colors = $statusColors[$status['id']] ?? $statusColors['todo'];
            @endphp
            <div class="flex-shrink-0 w-80 flex flex-col rounded-xl border border-gray-200 dark:border-gray-800 {{ $colors['bg'] }}">
                
                <!-- Column Header -->
                <div class="px-5 py-4 flex items-center justify-between" style="background: linear-gradient(135deg, {{ $colors['from'] }}, {{ $colors['to'] }}); border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
                    <h3 class="font-bold text-base text-white tracking-wide">{{ $status['title'] }}</h3>
                    <span class="flex items-center justify-center min-w-[28px] h-7 px-2 rounded-full text-xs font-bold" style="background: rgba(255,255,255,0.25); color: #fff;">
                        {{ $status['records']->count() }}
                    </span>
                </div>

                <!-- Column Body -->
                <div class="flex-1 space-y-3 p-3 min-h-[150px]">
                    @foreach($status['records'] as $task)
                        <a href="{{ \App\Filament\Employee\Resources\TaskResource::getUrl('edit', ['record' => $task->id]) }}" 
                           class="block group rounded-lg p-4 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md
                                  bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm relative">
                            
                            <div class="font-bold text-sm text-gray-800 dark:text-gray-100 mb-2 leading-tight break-words group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ $task->title }}
                            </div>
                            
                            @if($task->project?->name)
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-3 truncate">
                                    <x-heroicon-o-folder class="w-3.5 h-3.5 opacity-70 flex-shrink-0" />
                                    <span class="truncate">{{ $task->project->name }}</span>
                                </div>
                            @endif
                            
                            @if($task->due_date)
                                @php
                                    $dueDate = \Carbon\Carbon::parse($task->due_date);
                                @endphp
                                <div class="flex items-center gap-1.5 text-[11px] mt-2 px-2.5 py-1 rounded-md w-fit font-medium
                                            {{ $dueDate->isPast() ? 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400 border border-red-100 dark:border-red-800/50' : 'bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700' }}">
                                    <x-heroicon-o-calendar class="w-3.5 h-3.5" />
                                    {{ $dueDate->format('Y-m-d') }}
                                </div>
                            @endif
                        </a>
                    @endforeach
                    
                    @if($status['records']->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-gray-400 dark:text-gray-500">
                            <x-heroicon-o-clipboard-document-list class="w-10 h-10 mb-2 opacity-50" />
                            <span class="text-xs font-medium">{{ __('filament.kanban.no_tasks') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
