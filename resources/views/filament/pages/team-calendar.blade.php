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

    <div id="team-calendar-wrapper" dir="ltr">
        <div id="team-calendar-loading" class="flex items-center justify-center p-12 text-gray-500 dark:text-gray-400">
            <svg class="animate-spin h-6 w-6 me-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span>{{ __('filament.gantt.loading') }}</span>
        </div>
        <div id="team-calendar-error" class="hidden p-8 text-center text-red-500 dark:text-red-400"></div>
        <div id="team-calendar"></div>
    </div>

    @script
    <script>
        var calendarEvents = @js(json_decode($events));

        function loadFullCalendar() {
            if (window.__fullCalendarPromise) {
                return window.__fullCalendarPromise;
            }

            window.__fullCalendarPromise = new Promise(function (resolve, reject) {
                if (typeof FullCalendar !== 'undefined' && FullCalendar.Calendar) {
                    resolve();
                    return;
                }

                var script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js';
                script.async = true;
                script.onload = function () { resolve(); };
                script.onerror = function () {
                    window.__fullCalendarPromise = null;
                    reject(new Error('Failed to load FullCalendar library'));
                };
                document.head.appendChild(script);
            });

            return window.__fullCalendarPromise;
        }

        function renderTeamCalendar() {
            var calendarEl = document.getElementById('team-calendar');
            var loadingEl = document.getElementById('team-calendar-loading');
            var errorEl = document.getElementById('team-calendar-error');

            if (!calendarEl) {
                return;
            }

            if (typeof FullCalendar === 'undefined' || !FullCalendar.Calendar) {
                if (loadingEl) loadingEl.classList.add('hidden');
                if (errorEl) {
                    errorEl.classList.remove('hidden');
                    errorEl.textContent = '{{ __('filament.gantt.no_tasks') }}';
                }
                return;
            }

            if (loadingEl) loadingEl.classList.add('hidden');
            if (errorEl) errorEl.classList.add('hidden');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: '{{ app()->getLocale() }}',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: calendarEvents,
                eventDisplay: 'block',
                displayEventTime: false,
            });
            calendar.render();
        }

        loadFullCalendar()
            .then(renderTeamCalendar)
            .catch(function (err) {
                var loadingEl = document.getElementById('team-calendar-loading');
                var errorEl = document.getElementById('team-calendar-error');
                if (loadingEl) loadingEl.classList.add('hidden');
                if (errorEl) {
                    errorEl.classList.remove('hidden');
                    errorEl.textContent = '{{ __('filament.gantt.no_tasks') }}';
                }
                console.error(err);
            });
    </script>
    @endscript
</x-filament-panels::page>
