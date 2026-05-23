<?php
$logFile = 'C:\Users\Abdulhady\.gemini\antigravity-ide\brain\541690e6-3069-44b1-a91b-ed333eb52512\.system_generated\logs\transcript.jsonl';
$lines = file($logFile);
foreach ($lines as $line) {
    $data = json_decode($line, true);
    if (isset($data['tool_calls'])) {
        foreach ($data['tool_calls'] as $call) {
            if ($call['name'] === 'write_to_file' && strpos($call['args']['TargetFile'], 'filament-theme.css') !== false) {
                if (strlen($call['args']['CodeContent']) > 20000) {
                    file_put_contents('d:/Tecjno-Injaz/lite_erp_systeam/resources/css/filament-theme.css', $call['args']['CodeContent']);
                    echo 'Restored original file of length: ' . strlen($call['args']['CodeContent']);
                    exit;
                }
            }
        }
    }
}
echo 'Not found';
