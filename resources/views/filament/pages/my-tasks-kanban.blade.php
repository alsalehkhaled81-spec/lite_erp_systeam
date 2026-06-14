<x-filament-panels::page>
    <div x-data="{ draggedTaskId: null, dropIndex: 0 }">
        <div class="flex gap-5 overflow-x-auto pb-6 scroll-smooth" style="scrollbar-width: thin;">
            @foreach($statuses as $status)
                @php
                    $statusColors = [
                        'todo'        => ['from' => '#6366f1', 'to' => '#818cf8', 'bg' => 'bg-indigo-50/50 dark:bg-indigo-900/10'],
                        'in_progress' => ['from' => '#f59e0b', 'to' => '#fbbf24', 'bg' => 'bg-amber-50/50 dark:bg-amber-900/10'],
                        'review'      => ['from' => '#3b82f6', 'to' => '#60a9fa', 'bg' => 'bg-blue-50/50 dark:bg-blue-900/10'],
                        'done'        => ['from' => '#10b981', 'to' => '#34d399', 'bg' => 'bg-emerald-50/50 dark:bg-emerald-900/10'],
                    ];
                    $colors = $statusColors[$status['id']] ?? $statusColors['todo'];
                @endphp
                <div class="flex-shrink-0 flex flex-col rounded-xl border border-gray-200 dark:border-gray-800 {{ $colors['bg'] }}"
                     style="width: 320px;">

                    <div class="px-5 py-4 flex items-center justify-center gap-3 shrink-0" style="background: linear-gradient(135deg, {{ $colors['from'] }}, {{ $colors['to'] }}); border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
                        <h3 class="font-bold text-base text-white tracking-wide">{{ $status['title'] }}</h3>
                        <span class="flex items-center justify-center min-w-[28px] h-7 px-2 rounded-full text-xs font-bold" style="background: rgba(255,255,255,0.25); color: #fff;">
                            {{ $status['records']->count() }}
                        </span>
                    </div>

                    <div class="p-3 kanban-column transition-colors duration-200"
                         style="min-height: 500px;"
                         data-status="{{ $status['id'] }}"
                         x-on:dragover.prevent="
                             $event.dataTransfer.dropEffect = 'move';
                             $el.classList.add('ring-2','ring-indigo-400','ring-opacity-60');
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
                             ind.className = 'drop-indicator h-0.5 bg-indigo-400 rounded-full mb-3';
                             if ($event.clientY < rect.top + rect.height / 2) {
                                 card.parentNode.insertBefore(ind, card);
                             } else {
                                 card.after(ind);
                             }
                         "
                         x-on:dragleave="$el.contains($event.relatedTarget) || ($el.classList.remove('ring-2','ring-indigo-400','ring-opacity-60'), $el.querySelectorAll('.drop-indicator').forEach(function(el){el.remove();}))"
                         x-on:drop.prevent="
                             $el.classList.remove('ring-2','ring-indigo-400','ring-opacity-60');
                             $el.querySelectorAll('.drop-indicator').forEach(function(el){el.remove();});
                             if (draggedTaskId) {
                                 $wire.updateTaskStatus(draggedTaskId, $el.dataset.status, dropIndex);
                                 draggedTaskId = null;
                             }
                         ">
                        <div class="space-y-3">
                        @foreach($status['records'] as $task)
                            <div class="group rounded-lg p-4 transition-all duration-200 cursor-grab hover:-translate-y-0.5 hover:shadow-md
                                        bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm relative
                                        kanban-card active:cursor-grabbing active:shadow-lg text-center"
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

                                <a href="{{ \App\Filament\Employee\Resources\TaskResource::getUrl('edit', ['record' => $task->id]) }}"
                                   class="font-bold text-sm text-gray-800 dark:text-gray-100 mb-2 leading-tight break-words group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors hover:underline block">
                                    {{ $task->title }}
                                </a>

                                @if($task->project?->name)
                                    <div class="flex items-center justify-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-3">
                                        <x-heroicon-o-folder class="w-3.5 h-3.5 opacity-70 flex-shrink-0" />
                                        <span class="truncate">{{ $task->project->name }}</span>
                                    </div>
                                @endif

                                @if($task->due_date)
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($task->due_date);
                                    @endphp
                                    <div class="flex items-center justify-center gap-1.5 text-[11px] mt-2 mx-auto px-2.5 py-1 rounded-md w-fit font-medium
                                                {{ $dueDate->isPast() ? 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400 border border-red-100 dark:border-red-800/50' : 'bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700' }}">
                                        <x-heroicon-o-calendar class="w-3.5 h-3.5" />
                                        {{ $dueDate->format('Y-m-d') }}
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        @if($status['records']->isEmpty())
                            <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500" style="min-height: 400px;">
                                <x-heroicon-o-clipboard-document-list class="w-10 h-10 mb-2 opacity-50" />
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
