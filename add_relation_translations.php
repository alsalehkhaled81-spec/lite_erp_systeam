<?php

$relations = [
    'InvoicesRelationManager' => 'client_invoices',
    'SkillsRelationManager' => 'employee_skills',
    'EmployeesRelationManager' => 'project_team',
    'TasksRelationManager' => 'project_tasks',
];

$dir = new RecursiveDirectoryIterator('d:\Tecjno-Injaz\lite_erp_systeam\app\Filament');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+RelationManager\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach ($files as $file) {
    $file = $file[0];
    
    $content = file_get_contents($file);
    $basename = basename($file, '.php');

    if (!isset($relations[$basename])) continue;

    $key = $relations[$basename];

    // Check if Model is imported
    if (strpos($content, 'use Illuminate\Database\Eloquent\Model;') === false) {
        $content = preg_replace('/use Filament\\\\/', "use Illuminate\Database\Eloquent\Model;\nuse Filament\\", $content, 1);
    }

    $methodsToAdd = [];

    if (strpos($content, 'function getTitle') === false) {
        $methodsToAdd[] = "    public static function getTitle(Model \$ownerRecord, string \$pageClass): string\n    {\n        return __('filament.relation.{$key}');\n    }";
    }

    if (!empty($methodsToAdd)) {
        // Inject methods before the last closing brace
        $injection = "\n" . implode("\n\n", $methodsToAdd) . "\n";
        $content = preg_replace('/}(?!.*})/s', $injection . "}", $content);
        file_put_contents($file, $content);
        echo "Updated $basename in " . dirname($file) . "\n";
    }
}
