<x-filament-panels::page>
    <div class="rounded-2xl overflow-hidden bg-white/60 dark:bg-gray-800/50 backdrop-blur-xl border border-gray-200/50 dark:border-gray-700/30 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shadow-sm" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ __('filament.kanban.ai_evaluation') }}</h3>
        </div>
        <div class="prose dark:prose-invert max-w-none whitespace-pre-wrap text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ $evaluation }}</div>
    </div>
</x-filament-panels::page>
