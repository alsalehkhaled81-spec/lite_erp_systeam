<?php

$resources = [
    'ClientResource' => 'client',
    'ExpenseResource' => 'expense',
    'InvoiceResource' => 'invoice',
    'PayrollResource' => 'payroll',
    'LeaveResource' => 'leave',
    'ReportResource' => 'report',
    'TaskResource' => 'task',
    'DepartmentResource' => 'department',
    'EmployeeResource' => 'employee',
    'ResumeResource' => 'resume',
    'SkillResource' => 'skill',
    'ProjectResource' => 'project',
    'RoleResource' => 'role',
    'UserResource' => 'user',
];

$dir = new RecursiveDirectoryIterator('d:\Tecjno-Injaz\lite_erp_systeam\app\Filament');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+Resource\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach ($files as $file) {
    $file = $file[0];
    
    $content = file_get_contents($file);
    $basename = basename($file, '.php');

    if (!isset($resources[$basename])) continue;

    $modelKey = $resources[$basename];
    $pluralKey = $modelKey === 'leave' ? 'leaves' : $modelKey . 's';

    $methodsToAdd = [];

    if (strpos($content, 'function getModelLabel') === false) {
        $methodsToAdd[] = "    public static function getModelLabel(): string\n    {\n        return __('filament.model.{$modelKey}');\n    }";
    }

    if (strpos($content, 'function getPluralModelLabel') === false) {
        $methodsToAdd[] = "    public static function getPluralModelLabel(): string\n    {\n        return __('filament.model.{$pluralKey}');\n    }";
    }

    if (strpos($content, 'function getNavigationLabel') === false) {
        $methodsToAdd[] = "    public static function getNavigationLabel(): string\n    {\n        return __('filament.nav.{$pluralKey}');\n    }";
    }

    if (!empty($methodsToAdd)) {
        // Inject methods before the last closing brace
        $injection = "\n" . implode("\n\n", $methodsToAdd) . "\n";
        $content = preg_replace('/}(?!.*})/s', $injection . "}", $content);
        file_put_contents($file, $content);
        echo "Updated $basename in " . dirname($file) . "\n";
    }
}
