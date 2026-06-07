<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/Filament'));
$usedKeys = [];
foreach ($files as $file) {
    if ($file->getExtension() !== 'php') continue;
    $content = file_get_contents($file->getPathname());
    preg_match_all("/__\(\s*'(filament\.[^']+)'\s*\)/", $content, $matches);
    foreach ($matches[1] as $key) $usedKeys[$key] = true;
    preg_match_all('/__\(\s*"(filament\.[^"]+)"\s*\)/', $content, $matches2);
    foreach ($matches2[1] as $key) $usedKeys[$key] = true;
}

function flattenKeys($arr, $prefix = '') {
    $result = [];
    foreach ($arr as $k => $v) {
        $fullKey = $prefix ? $prefix . '.' . $k : $k;
        if (is_array($v)) {
            $result = array_merge($result, flattenKeys($v, $fullKey));
        } else {
            $result[$fullKey] = $v;
        }
    }
    return $result;
}

$ar = flattenKeys(include 'lang/ar/filament.php');
$en = flattenKeys(include 'lang/en/filament.php');

$usedNormalized = [];
foreach ($usedKeys as $key => $_) {
    $shortKey = substr($key, strlen('filament.'));
    $usedNormalized[$shortKey] = $key;
}

echo "=== MISSING FROM EN (keys used in code but not in en/filament.php) ===\n";
$missingEn = [];
foreach ($usedNormalized as $short => $full) {
    if (!isset($en[$short])) $missingEn[] = $short;
}
sort($missingEn);
foreach ($missingEn as $k) echo $k . "\n";

echo "\n=== MISSING FROM AR (keys used in code but not in ar/filament.php) ===\n";
$missingAr = [];
foreach ($usedNormalized as $short => $full) {
    if (!isset($ar[$short])) $missingAr[] = $short;
}
sort($missingAr);
foreach ($missingAr as $k) echo $k . "\n";
