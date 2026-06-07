<?php

namespace App\Services;

use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;

class PayslipPdfService
{
    public function generate(Payroll $payroll)
    {
        $payroll->load('employee.user');
        
        $arabic = new \Arphp\Glyphs();

        // Generate a short AI motivational note for the payslip
        $aiNote = null;
        try {
            $aiService = app(\App\Services\AiService::class);
            $prompt = "Write a very short (one sentence) thank you note in English to an employee named {$payroll->employee->user->name} for receiving their salary for the month of {$payroll->month_year}. Be friendly and motivational.";
            
            $response = $aiService->chat([
                ['role' => 'system', 'content' => 'You are a kind HR manager who writes very short thank you and encouragement notes to employees in English.'],
                ['role' => 'user', 'content' => $prompt]
            ]);

            if ($response && isset($response['choices'][0]['message']['content'])) {
                $aiNote = $response['choices'][0]['message']['content'];
            }
        } catch (\Exception $e) {
            // Silently fail AI feature so PDF generation isn't interrupted
        }

        $payroll->employee->user->name = $arabic->utf8Glyphs($payroll->employee->user->name);
        if ($payroll->employee->job_title) {
            $payroll->employee->job_title = $arabic->utf8Glyphs($payroll->employee->job_title);
        }
        if ($payroll->employee->department && $payroll->employee->department->name) {
            $payroll->employee->department->name = $arabic->utf8Glyphs($payroll->employee->department->name);
        }

        $data = [
            'payroll' => $payroll,
            'employee' => $payroll->employee,
            'user' => $payroll->employee->user,
            'company' => $arabic->utf8Glyphs(config('app.name', 'ERP-Lite')),
            'ai_note' => $aiNote ? $arabic->utf8Glyphs($aiNote) : null,
        ];

        $pdf = Pdf::loadView('pdf.payslip', $data);

        $filename = 'payslip_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $payroll->employee->user->name) . '_' . $payroll->month_year . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}
