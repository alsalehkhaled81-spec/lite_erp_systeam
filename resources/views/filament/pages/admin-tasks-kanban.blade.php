<x-filament-panels::page>
    <div x-data="{ draggedTaskId: null, dropIndex: 0 }">
        <div class="flex flex-wrap gap-3 mb-5 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('filament.filters.filter_by_project') }}</label>
                <select wire:model.live="filterProjectId" class="fi-input block w-full rounded-lg text-sm px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    <option value="">-- {{ __('filament.filters.all') }} --</option>
                    @foreach($projects as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('filament.filters.filter_by_employee') }}</label>
                <select wire:model.live="filterEmployeeId" class="fi-input block w-full rounded-lg text-sm px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    <option value="">-- {{ __('filament.filters.all') }} --</option>
                    @foreach($employees as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex gap-5 overflow-x-auto pb-6 scroll-smooth" style="scrollbar-width: thin;">
            @foreach($statuses as $status)
                @php
                    $statusColors = [
                        'todo'        => ['from' => '#6366f1', 'to' => '#818cf8', 'bg' => 'rgba(99,102,241,0.05)', 'border' => 'rgba(99,102,241,0.12)'],
                        'in_progress' => ['from' => '#f59e0b', 'to' => '#fbbf24', 'bg' => 'rgba(245,158,11,0.05)', 'border' => 'rgba(245,158,11,0.12)'],
                        'review'      => ['from' => '#3b82f6', 'to' => '#60a9fa', 'bg' => 'rgba(59,130,246,0.05)', 'border' => 'rgba(59,130,246,0.12)'],
                        'done'        => ['from' => '#10b981', 'to' => '#34d399', 'bg' => 'rgba(16,185,129,0.05)', 'border' => 'rgba(16,185,129,0.12)'],
                    ];
                    $colors = $statusColors[$status['id']] ?? $statusColors['todo'];
                @endphp
                <div class="flex-shrink-0 flex flex-col rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-lg"
                     style="width: 320px; background: {{ $colors['bg'] }}; border: 1px solid {{ $colors['border'] }};">

                    <div class="px-5 py-4 flex items-center justify-center gap-3 shrink-0"
                         style="background: linear-gradient(135deg, {{ $colors['from'] }}, {{ $colors['to'] }});">
                        <h3 class="font-bold text-base text-white tracking-wide">{{ $status['title'] }}</h3>
                        <span class="flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold"
                              style="background: rgba(255,255,255,0.25); color: #fff;">
                            {{ $status['records']->count() }}
                        </span>
                    </div>

                    <div class="p-4 kanban-column transition-colors duration-200 rounded-b-2xl"
                         style="min-height: 500px;"
                         data-status="{{ $status['id'] }}"
                         x-on:dragover.prevent="
                             $event.dataTransfer.dropEffect = 'move';
                             $el.classList.add('ring-2','ring-blue-400','ring-opacity-60');
                             const cards = [...$el.querySelectorAll('.kanban-card')].filter(c => !c.classList.contains('opacity-50'));
                             dropIndex = cards.length;
                             cards.forEach(function(card, i) {
                                 const rect = card.getBoundingClientRect();
                                 if ($event.clientY < rect.top + rect.height / 2) {
                                     if (i < dropIndex) dropIndex = i;
                                 }
                             });
                             $el.querySelectorAll('.drop-indicator').forEach(function(el) { el.remove(); });
                             if (cards.length === 0) return;
                             const card = cards[Math.min(dropIndex, cards.length - 1)];
                             const rect = card.getBoundingClientRect();
                             const ind = document.createElement('div');
                             ind.className = 'drop-indicator h-0.5 bg-blue-400 rounded-full mb-3';
                             if ($event.clientY < rect.top + rect.height / 2) {
                                 card.parentNode.insertBefore(ind, card);
                             } else {
                                 card.after(ind);
                             }
                         "
                         x-on:dragleave="$el.contains($event.relatedTarget) || ($el.classList.remove('ring-2','ring-blue-400','ring-opacity-60'), $el.querySelectorAll('.drop-indicator').forEach(function(el){el.remove();}))"
                         x-on:drop.prevent="
                             $el.classList.remove('ring-2','ring-blue-400','ring-opacity-60');
                             $el.querySelectorAll('.drop-indicator').forEach(function(el){el.remove();});
                             if (draggedTaskId) {
                                 $wire.updateTaskStatus(draggedTaskId, $el.dataset.status, dropIndex);
                                 draggedTaskId = null;
                             }
                         ">
                        <div class="space-y-3">
                        @foreach($status['records'] as $task)
                            @php
                                $priorityColors = [
                                    'high'   => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-300', 'dot' => 'bg-red-500'],
                                    'medium' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/30', 'text' => 'text-yellow-700 dark:text-yellow-300', 'dot' => 'bg-yellow-500'],
                                    'low'    => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-300', 'dot' => 'bg-blue-500'],
                                ];
                                $pColor = $priorityColors[$task->priority ?? 'medium'] ?? $priorityColors['medium'];
                            @endphp
                            <div class="group rounded-xl p-4 transition-all duration-200 cursor-grab hover:-translate-y-0.5 hover:shadow-md
                                        bg-white/80 dark:bg-gray-800/60 border border-gray-100/80 dark:border-gray-700/40
                                        backdrop-blur-sm kanban-card active:cursor-grabbing active:shadow-lg text-center"
                                 data-task-id="{{ $task->id }}"
                                 draggable="true"
                                 x-on:dragstart="
                                     draggedTaskId = {{ $task->id }};
                                     $event.dataTransfer.effectAllowed = 'move';
                                     $event.dataTransfer.setData('text/plain', '{{ $task->id }}');
                                     $el.classList.add('opacity-50','scale-95');
                                 "
                                 x-on:dragend="
                                     draggedTaskId = null;
                                     $el.classList.remove('opacity-50','scale-95');
                                     document.querySelectorAll('.drop-indicator').forEach(function(el){el.remove();});
                                 ">
                                <div class="flex flex-col items-center gap-1.5 mb-1.5">
                                    <a href="{{ \App\Filament\Resources\TaskResource::getUrl('edit', ['record' => $task->id]) }}"
                                       class="font-semibold text-sm text-gray-800 dark:text-gray-100 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors hover:underline">
                                        {{ $task->title }}
                                    </a>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full {{ $pColor['bg'] }} {{ $pColor['text'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $pColor['dot'] }}"></span>
                                        {{ __('filament.priority.' . ($task->priority ?? 'medium')) }}
                                    </span>
                                </div>
                                @if($task->project?->name)
                                    <div class="flex items-center justify-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1">
                                        <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                        </svg>
                                        {{ $task->project->name }}
                                    </div>
                                @endif
                                @if($task->employee?->user)
                                    <div class="flex items-center justify-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1">
                                        <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ $task->employee->user->name }}
                                    </div>
                                @endif
                                @if($task->due_date)
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($task->due_date);
                                    @endphp
                                    <div class="flex items-center justify-center gap-1.5 text-xs mt-2 px-2.5 py-1 rounded-full mx-auto
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
                            <div class="flex flex-col items-center justify-center text-gray-400/60 dark:text-gray-500/60" style="min-height: 400px;">
                                <svg class="w-10 h-10 mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span class="text-xs font-medium">{{ __('filament.kanban.no_tasks') }}</span>
                            </div>
                        @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
