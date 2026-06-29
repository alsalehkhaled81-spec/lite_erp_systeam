<?php
$file = __DIR__ . '/lang/ar/filament.php';
$content = file_get_contents($file);

$add = "
        'filters' => 'فلترة الإحصائيات',
        'start_date' => 'من تاريخ',
        'end_date' => 'إلى تاريخ',
        'tasks_count' => 'عدد المهام',
        'overdue_tasks' => 'المهام المتأخرة',
        'overdue_tasks_desc' => 'مهام تجاوزت تاريخ الاستحقاق',
        'overdue_tasks_table' => 'المهام المتأخرة',
        'projects_count' => 'المشاريع',
        'employee_performance' => 'أداء الموظفين',
        'employee_performance_desc' => 'إحصائيات إنجاز المهام للموظفين في قسمك ضمن الفترة المحددة',
        'employee_name' => 'اسم الموظف',
        'assigned_tasks' => 'المهام المسندة',
        'completed_tasks' => 'المهام المنجزة',
        'completion_rate' => 'نسبة الإنجاز',
";

// We previously saw "'save_profile' => '...'," - I'll just append before the last '];' of 'widgets'
// Since I'm not 100% sure of the exact 'save_profile' Arabic string, I'll search for "'save_profile'" and append after that line.
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, "'save_profile'") !== false) {
        array_splice($lines, $i + 1, 0, $add);
        break;
    }
}
$newContent = implode("\n", $lines);
file_put_contents($file, $newContent);
echo "Translations added successfully!\n";
