<x-filament-panels::page>
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
        (function () {
            const FRAPPE_CSS = 'https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.css';
            const FRAPPE_JS = 'https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js';

            // Tasks are injected from PHP directly as a JS array (no manual parsing needed).
            const allTasks = @js($tasks);

            // The frappe-gantt library must be loaded dynamically because this panel
            // uses SPA navigation: <script src> tags placed in the page body are NOT
            // re-executed when Livewire morphs the content, which left the chart blank.
            function loadFrappeGantt() {
                if (window.__frappeGanttPromise) {
                    return window.__frappeGanttPromise;
                }

                window.__frappeGanttPromise = new Promise(function (resolve, reject) {
                    // The frappe-gantt UMD build exposes the global as `Gantt`,
                    // NOT `FrappeGantt`.
                    if (typeof Gantt !== 'undefined') {
                        resolve();
                        return;
                    }

                    if (!document.querySelector('link[data-frappe-gantt-css]')) {
                        const link = document.createElement('link');
                        link.rel = 'stylesheet';
                        link.href = FRAPPE_CSS;
                        link.setAttribute('data-frappe-gantt-css', '');
                        document.head.appendChild(link);
                    }

                    if (document.querySelector('script[data-frappe-gantt-js]')) {
                        const existing = document.querySelector('script[data-frappe-gantt-js]');
                        existing.addEventListener('load', resolve, { once: true });
                        existing.addEventListener('error', () => reject(new Error('load error')), { once: true });
                        return;
                    }

                    const script = document.createElement('script');
                    script.src = FRAPPE_JS;
                    script.setAttribute('data-frappe-gantt-js', '');
                    script.onload = function () { resolve(); };
                    script.onerror = function () { reject(new Error('Failed to load the Gantt library')); };
                    document.head.appendChild(script);

                    // Avoid waiting forever if the CDN is unreachable.
                    setTimeout(function () {
                        if (typeof Gantt === 'undefined') {
                            reject(new Error('timeout'));
                        }
                    }, 15000);
                });

                return window.__frappeGanttPromise;
            }

            let currentGantt = null;

            function showError(container, message) {
                container.innerHTML = '<div class="p-8 text-center text-red-500 dark:text-red-400"><p class="text-base font-medium">' + message + '</p><p class="mt-2 text-sm text-gray-500 dark:text-gray-400">تأكد من اتصالك بالإنترنت ثم أعد تحميل الصفحة.</p></div>';
            }

            function renderGantt(tasks) {
                const container = document.getElementById('gantt-container');
                const controlsDiv = document.getElementById('gantt-controls');
                if (!container || !controlsDiv) {
                    return;
                }

                container.innerHTML = '';
                controlsDiv.innerHTML = '';

                if (!tasks || tasks.length === 0) {
                    container.innerHTML = '<div class="p-8 text-center text-gray-500 dark:text-gray-400"><p class="text-lg font-medium">لا توجد مهام مرتبطة بهذا المشروع</p></div>';
                    return;
                }

                const svgEl = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svgEl.setAttribute('id', 'gantt-svg');
                svgEl.setAttribute('width', '100%');
                svgEl.setAttribute('height', '100%');
                container.appendChild(svgEl);

                try {
                    currentGantt = new Gantt('#gantt-svg', tasks, {
                        view_mode: 'Day',
                        date_format: 'YYYY-MM-DD',
                        custom_popup_html: function (task) {
                            const t = tasks.find(function (i) { return i.id == task.id; });
                            if (!t) {
                                return '';
                            }

                            return '<div style="direction:rtl;text-align:right;font-family:Cairo,sans-serif;padding:12px;min-width:220px;">' +
                                '<h4 style="margin:0 0 8px;font-size:14px;font-weight:bold;">' + t.name + '</h4>' +
                                '<p style="margin:2px 0;font-size:12px;">المشروع: ' + (t.project || '-') + '</p>' +
                                '<p style="margin:2px 0;font-size:12px;">الموظف: ' + (t.employee || '-') + '</p>' +
                                '<p style="margin:2px 0;font-size:12px;">' + t.start + ' \u2192 ' + t.end + '</p>' +
                                '<p style="margin:2px 0;font-size:12px;">الحالة: ' + t.status + '</p>' +
                                '<a href="' + t.url + '" style="display:inline-block;margin-top:8px;padding:4px 12px;background:#3b82f6;color:#fff;border-radius:6px;text-decoration:none;font-size:12px;">عرض المهمة</a>' +
                            '</div>';
                        },
                    });
                } catch (err) {
                    console.error('Gantt render error:', err);
                    container.innerHTML = '';
                    showError(container, 'حدث خطأ أثناء رسم مخطط جانت: ' + (err && err.message ? err.message : err));
                    return;
                }

                const viewModes = ['Quarter Day', 'Half Day', 'Day', 'Week', 'Month'];
                viewModes.forEach(function (mode) {
                    const btn = document.createElement('button');
                    btn.textContent = mode;
                    btn.className = 'px-3 py-1 text-xs rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition gantt-controls-btn';
                    if (mode === 'Day') {
                        btn.classList.add('bg-blue-500', 'text-white');
                    }
                    btn.addEventListener('click', function () {
                        if (!currentGantt) {
                            return;
                        }
                        currentGantt.change_view_mode(mode);
                        controlsDiv.querySelectorAll('.gantt-controls-btn').forEach(function (b) {
                            b.classList.remove('bg-blue-500', 'text-white');
                        });
                        btn.classList.add('bg-blue-500', 'text-white');
                    });
                    controlsDiv.appendChild(btn);
                });
            }

            function init() {
                const container = document.getElementById('gantt-container');
                if (!container) {
                    return;
                }

                container.innerHTML = '<div class="p-8 text-center text-gray-500 dark:text-gray-400"><p class="text-base font-medium">{{ __('filament.gantt.loading') }}</p></div>';

                loadFrappeGantt()
                    .then(function () {
                        renderGantt(allTasks);

                        const filterSelect = document.getElementById('gantt-project-filter');
                        if (filterSelect) {
                            filterSelect.addEventListener('change', function () {
                                const projectId = this.value;
                                if (projectId) {
                                    renderGantt(allTasks.filter(function (t) {
                                        return t.project_id == projectId;
                                    }));
                                } else {
                                    renderGantt(allTasks);
                                }
                            });
                        }
                    })
                    .catch(function () {
                        showError(container, 'تعذّر تحميل مكتبة مخطط جانت من الإنترنت.');
                    });
            }

            init();
        })();
    </script>
    @endscript
</x-filament-panels::page>
