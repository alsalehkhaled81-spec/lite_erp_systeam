<?php

use App\Models\Task;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Client;
use App\Services\DashboardExportService;
use App\Support\Arabic;

function capturePdf(DashboardExportService $service): string
{
    $response = $service->generatePdf();

    ob_start();
    $response->sendContent();
    $content = ob_get_clean();

    return $content;
}

function hasArabicPresentationForms(string $text): bool
{
    for ($i = 0, $len = mb_strlen($text, 'UTF-8'); $i < $len; $i++) {
        $code = mb_ord(mb_substr($text, $i, 1, 'UTF-8'), 'UTF-8');
        if ($code >= 0xFB50 && $code <= 0xFEFF) {
            return true;
        }
    }

    return false;
}

describe('Arabic shaping helper', function () {
    test('shape() converts Arabic text to presentation forms', function () {
        $raw = 'إجمالي الإيرادات';
        $shaped = Arabic::shape($raw);

        expect($shaped)->not->toBe($raw)
            ->and(hasArabicPresentationForms($shaped))->toBeTrue();
    });

    test('shape() leaves Latin text and numbers untouched', function () {
        expect(Arabic::shape('Total Revenue'))->toBe('Total Revenue')
            ->and(Arabic::shape('1234.56'))->toBe('1234.56');
    });

    test('shape() handles empty input safely', function () {
        expect(Arabic::shape(''))->toBe('');
    });

    test('glyph instance is reused (cached)', function () {
        $reflection = new ReflectionClass(Arabic::class);
        $property = $reflection->getMethod('glyph');
        $property->setAccessible(true);

        $first = $property->invoke(null);
        $second = $property->invoke(null);

        expect($first)->toBe($second);
    });
});

describe('@ar Blade directive', function () {
    test('shapes Arabic strings when rendered', function () {
        app()->setLocale('ar');

        $html = Illuminate\Support\Facades\Blade::render('@ar($text)', ['text' => __('filament.widgets.revenue_label')]);

        expect($html)->not->toBeEmpty()
            ->and(hasArabicPresentationForms($html))->toBeTrue();
    });

    test('passes Latin strings through unchanged', function () {
        app()->setLocale('en');

        $html = Illuminate\Support\Facades\Blade::render('@ar($text)', ['text' => 'Total Revenue']);

        expect(trim($html))->toBe('Total Revenue');
    });
});

describe('Dashboard PDF export', function () {
    test('generates a valid PDF in Arabic locale', function () {
        app()->setLocale('ar');

        $content = capturePdf(app(DashboardExportService::class));

        expect($content)->toStartWith('%PDF')
            ->and(str_contains($content, '%PDF'))->toBeTrue();
    });

    test('generates a valid PDF in English locale', function () {
        app()->setLocale('en');

        $content = capturePdf(app(DashboardExportService::class));

        expect($content)->toStartWith('%PDF');
    });

    test('generates a PDF with no data present', function () {
        app()->setLocale('ar');

        $content = capturePdf(app(DashboardExportService::class));

        expect($content)->toStartWith('%PDF');
    });

    test('generates a PDF with real data in both locales', function () {
        $client = Client::factory()->create();
        $project = Project::factory()->create();
        $employeeUser = \App\Models\User::factory()->create(['name' => 'Ahmed Ali']);
        $employee = Employee::factory()->create(['user_id' => $employeeUser->id]);

        Task::factory()->create([
            'title' => 'تطوير الواجهة',
            'project_id' => $project->id,
            'employee_id' => $employee->id,
        ]);
        Invoice::factory()->create(['status' => 'paid', 'client_id' => $client->id, 'amount' => 5000]);
        Expense::factory()->create(['amount' => 1200]);

        foreach (['ar', 'en'] as $locale) {
            app()->setLocale($locale);

            $content = capturePdf(app(DashboardExportService::class));

            expect($content)->toStartWith('%PDF')
                ->and(strlen($content))->toBeGreaterThan(1000);
        }
    });

    test('Arabic report view applies shaping to every text block', function () {
        app()->setLocale('ar');

        Client::factory()->create();
        Project::factory()->create();

        $html = view('pdf.dashboard-report', [
            'company' => config('app.name', 'ERP'),
            'title' => __('filament.actions.export_dashboard_pdf'),
            'generatedAt' => now()->translatedFormat('Y-m-d H:i'),
            'kpis' => [
                ['label' => __('filament.widgets.net_profit'), 'value' => '$1,000.00'],
            ],
            'monthlyData' => [
                ['month' => now()->translatedFormat('M Y'), 'revenue' => 1000, 'expense' => 500, 'net' => 500],
            ],
            'activities' => collect([
                ['color' => 'badge-info', 'icon' => 'T', 'description' => 'مراجعة الكود', 'time' => 'منذ يوم'],
            ]),
            'employeeStats' => [
                ['name' => 'سارة محمد', 'tasks' => 5, 'rate' => 80],
            ],
        ])->render();

        expect(hasArabicPresentationForms($html))->toBeTrue();
    });
});
