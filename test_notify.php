<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$report = \App\Models\Report::with('receiver.user')->first();
if ($report && $report->receiver && $report->receiver->user) {
    \Filament\Notifications\Notification::make()
        ->title('Test Notification')
        ->body('Testing the notification system')
        ->icon('heroicon-o-bell')
        ->iconColor('success')
        ->sendToDatabase($report->receiver->user);
    echo "Done! Sent to " . $report->receiver->user->email . "\n";
} else {
    echo "Report or receiver not found.\n";
}
