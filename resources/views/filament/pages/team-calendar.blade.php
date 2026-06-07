<x-filament-panels::page>
    <div class="flex gap-4 mb-4 flex-wrap">
        <div class="flex items-center gap-2 text-xs">
            <span class="w-3 h-3 rounded-sm" style="background:#3b82f6;"></span>
            <span class="text-gray-600 dark:text-gray-400">{{ __('filament.leave_type.Annual') }}</span>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="w-3 h-3 rounded-sm" style="background:#ef4444;"></span>
            <span class="text-gray-600 dark:text-gray-400">{{ __('filament.leave_type.Sick') }}</span>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="w-3 h-3 rounded-sm" style="background:#f59e0b;"></span>
            <span class="text-gray-600 dark:text-gray-400">{{ __('filament.leave_type.Emergency') }}</span>
        </div>
    </div>
    <div id="team-calendar" dir="ltr"></div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    @script
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var events = {!! $events !!};
        var calendarEl = document.getElementById('team-calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ar',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,weekGrid'
            },
            events: events,
            eventDisplay: 'block',
            displayEventTime: false,
        });
        calendar.render();
    });
    </script>
    @endscript
</x-filament-panels::page>
