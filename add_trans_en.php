<?php
$file = __DIR__ . '/lang/en/filament.php';
$content = file_get_contents($file);

$add = "
        'filters' => 'Statistics Filters',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'tasks_count' => 'Tasks Count',
        'overdue_tasks' => 'Overdue Tasks',
        'overdue_tasks_desc' => 'Tasks that have passed their due date',
        'overdue_tasks_table' => 'Overdue Tasks',
        'projects_count' => 'Projects',
        'employee_performance' => 'Employee Performance',
        'employee_performance_desc' => 'Task completion statistics for employees in your department within the specified period',
        'employee_name' => 'Employee Name',
        'assigned_tasks' => 'Assigned Tasks',
        'completed_tasks' => 'Completed Tasks',
        'completion_rate' => 'Completion Rate',
";

$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, "'save_profile'") !== false) {
        array_splice($lines, $i + 1, 0, $add);
        break;
    }
}
$newContent = implode("\n", $lines);
file_put_contents($file, $newContent);
echo "Translations added successfully to EN!\n";
