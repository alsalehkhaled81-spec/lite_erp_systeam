<x-filament-panels::page>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.css">
    <script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>

    <div class="mb-4">
        <div class="flex items-center gap-4 mb-4">
            <div class="flex-1 max-w-xs">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('filament.filters.filter_by_project') }}</label>
                <select id="gantt-project-filter" class="fi-input block w-full rounded-lg text-sm px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    <option value="">-- {{ __('filament.filters.all') ?? 'الكل' }} --</option>
                    @foreach($projects as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex gap-4 mb-4 flex-wrap">
            <div class="flex items-center gap-2 text-xs">
                <span class="w-3 h-3 rounded-sm" style="background:#6b7280;"></span>
                <span class="text-gray-600 dark:text-gray-400">{{ __('filament.status.todo') }}</span>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="w-3 h-3 rounded-sm" style="background:#f59e0b;"></span>
                <span class="text-gray-600 dark:text-gray-400">{{ __('filament.status.in_progress') }}</span>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="w-3 h-3 rounded-sm" style="background:#3b82f6;"></span>
                <span class="text-gray-600 dark:text-gray-400">{{ __('filament.status.review') }}</span>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="w-3 h-3 rounded-sm" style="background:#10b981;"></span>
                <span class="text-gray-600 dark:text-gray-400">{{ __('filament.status.done') }}</span>
            </div>
        </div>
    </div>

    <div id="gantt-controls" class="flex gap-2 mb-2 justify-end" style="direction: ltr;"></div>
    <div id="gantt-container" class="overflow-x-auto bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm" dir="ltr" style="min-height: 400px;"></div>

    @script
    <script>
        function initGanttChart() {
            if (typeof FrappeGantt === 'undefined') {
                setTimeout(initGanttChart, 200);
                return;
            }

            var allTasks = JSON.parse(@js($tasksJson));
            var currentGantt = null;

            function renderGantt(tasks) {
                var container = document.getElementById('gantt-container');
                var controlsDiv = document.getElementById('gantt-controls');
                if (!container || !controlsDiv) return;

                container.innerHTML = '';
                controlsDiv.innerHTML = '';

                if (!tasks || tasks.length === 0) {
                    container.innerHTML = '<div class="p-8 text-center text-gray-500 dark:text-gray-400"><p class="text-lg font-medium">لا توجد مهام مرتبطة بهذا المشروع</p></div>';
                    return;
                }

                var svgEl = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svgEl.setAttribute('id', 'gantt-svg');
                svgEl.setAttribute('width', '100%');
                svgEl.setAttribute('height', '100%');
                container.appendChild(svgEl);

                currentGantt = new FrappeGantt('#gantt-svg', tasks, {
                    view_mode: 'Day',
                    date_format: 'YYYY-MM-DD',
                    custom_popup_html: function(task) {
                        var t = tasks.find(function(i) { return i.id == task.id; });
                        if (!t) return '';

                        return '<div style="direction:rtl;text-align:right;font-family:Cairo,sans-serif;padding:12px;min-width:220px;">' +
                            '<h4 style="margin:0 0 8px;font-size:14px;font-weight:bold;">' + t.name + '</h4>' +
                            '<p style="margin:2px 0;font-size:12px;">المشروع: ' + (t.project || '-') + '</p>' +
                            '<p style="margin:2px 0;font-size:12px;">الموظف: ' + (t.employee || '-') + '</p>' +
                            '<p style="margin:2px 0;font-size:12px;">' + t.start + ' \u2192 ' + t.end + '</p>' +
                            '<p style="margin:2px 0;font-size:12px;">الحالة: ' + t.status + '</p>' +
                            '<a href="' + t.url + '" style="display:inline-block;margin-top:8px;padding:4px 12px;background:#3b82f6;color:#fff;border-radius:6px;text-decoration:none;font-size:12px;">عرض المهمة</a>' +
                        '</div>';
                    }
                });

                var viewModes = ['Quarter Day', 'Half Day', 'Day', 'Week', 'Month'];
                viewModes.forEach(function(mode) {
                    var btn = document.createElement('button');
                    btn.textContent = mode;
                    btn.className = 'px-3 py-1 text-xs rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition gantt-controls-btn';
                    if (mode === 'Day') btn.classList.add('bg-blue-500', 'text-white');
                    btn.addEventListener('click', function() {
                        currentGantt.change_view_mode(mode);
                        controlsDiv.querySelectorAll('.gantt-controls-btn').forEach(function(b) {
                            b.classList.remove('bg-blue-500', 'text-white');
                        });
                        btn.classList.add('bg-blue-500', 'text-white');
                    });
                    controlsDiv.appendChild(btn);
                });
            }

            renderGantt(allTasks);

            var filterSelect = document.getElementById('gantt-project-filter');
            if (filterSelect) {
                filterSelect.addEventListener('change', function() {
                    var projectId = this.value;
                    if (projectId) {
                        var filtered = allTasks.filter(function(t) {
                            return t.project_id == projectId;
                        });
                        renderGantt(filtered);
                    } else {
                        renderGantt(allTasks);
                    }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initGanttChart);
        } else {
            initGanttChart();
        }
    </script>
    @endscript
</x-filament-panels::page>
