<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/Filament'));
$keys = [];
foreach ($files as $file) {
    if ($file->getExtension() !== 'php') continue;
    $content = file_get_contents($file->getPathname());
    preg_match_all("/__\(\s*'(filament\.[^']+)'\s*\)/", $content, $matches);
    foreach ($matches[1] as $key) {
        $keys[$key] = true;
    }
    preg_match_all('/__\(\s*"(filament\.[^"]+)"\s*\)/', $content, $matches2);
    foreach ($matches2[1] as $key) {
        $keys[$key] = true;
    }
}
ksort($keys);
foreach (array_keys($keys) as $k) echo $k . "\n";
