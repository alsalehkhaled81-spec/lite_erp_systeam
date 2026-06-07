<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>{{ $company }} - {{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; direction: ltr; color: #1f2937; font-size: 13px; }
        .header { text-align: center; padding: 20px 0 15px; border-bottom: 3px solid #3b82f6; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #1e40af; margin-bottom: 4px; }
        .header p { color: #6b7280; font-size: 12px; }
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
        .kpi-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; text-align: center; }
        .kpi-card .value { font-size: 22px; font-weight: bold; color: #1e40af; }
        .kpi-card .label { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .kpi-card .trend { font-size: 10px; margin-top: 2px; }
        .kpi-card .trend.up { color: #16a34a; }
        .kpi-card .trend.down { color: #dc2626; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 15px; font-weight: bold; color: #1e40af; border-bottom: 2px solid #3b82f6; padding-bottom: 6px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { background: #1e40af; color: white; padding: 8px 10px; text-align: left; font-size: 11px; }
        td { padding: 6px 10px; border-bottom: 1px solid #e5e7eb; font-size: 11px; text-align: left; }
        tr:nth-child(even) { background: #f8fafc; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-gray { background: #f3f4f6; color: #374151; }
        .chart-placeholder { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 30px; text-align: center; color: #94a3b8; margin-bottom: 15px; }
        .footer { text-align: center; padding-top: 15px; border-top: 2px solid #e5e7eb; color: #9ca3af; font-size: 10px; }
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company }}</h1>
        <p>{{ $title }} - {{ $generatedAt }}</p>
    </div>

    <div class="section">
        <div class="section-title">{{ __('filament.widgets.dashboard_kpi_title') }}</div>
        <div class="kpi-grid">
            @foreach($kpis as $kpi)
            <div class="kpi-card">
                <div class="value">{{ $kpi['value'] }}</div>
                <div class="label">{{ $kpi['label'] }}</div>
                @if(isset($kpi['trend']))
                <div class="trend {{ $kpi['trend'] >= 0 ? 'up' : 'down' }}">
                    {{ $kpi['trend'] >= 0 ? '↑' : '↓' }} {{ abs($kpi['trend']) }}%
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="section">
        <div class="section-title">{{ __('filament.widgets.revenue_vs_expenses') }}</div>
        <table>
            <thead>
                <tr>
                    <th>{{ __('filament.columns.month') }}</th>
                    <th>{{ __('filament.widgets.revenue_label') }}</th>
                    <th>{{ __('filament.widgets.expenses_label') }}</th>
                    <th>{{ __('filament.widgets.net_profit') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyData as $month)
                <tr>
                    <td>{{ $month['month'] }}</td>
                    <td>${{ number_format($month['revenue'], 2) }}</td>
                    <td>${{ number_format($month['expense'], 2) }}</td>
                    <td style="color: {{ $month['net'] >= 0 ? '#16a34a' : '#dc2626' }}">
                        ${{ number_format($month['net'], 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="two-col">
        <div class="section">
            <div class="section-title">{{ __('filament.widgets.live_activity') }}</div>
            <table>
                <thead><tr><th>{{ __('filament.columns.type') }}</th><th>{{ __('filament.columns.description') }}</th><th>{{ __('filament.columns.date') }}</th></tr></thead>
                <tbody>
                    @foreach($activities->take(15) as $activity)
                    <tr>
                        <td><span class="badge {{ $activity['color'] }}">{{ $activity['icon'] }}</span></td>
                        <td>{{ $activity['description'] }}</td>
                        <td>{{ $activity['time'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">{{ __('filament.widgets.employee_heatmap') }}</div>
            <table>
                <thead><tr><th>{{ __('filament.columns.employee_name') }}</th><th>{{ __('filament.widgets.tasks_count') }}</th><th>{{ __('filament.widgets.completion_rate') }}</th></tr></thead>
                <tbody>
                    @foreach($employeeStats as $stat)
                    <tr>
                        <td>{{ $stat['name'] }}</td>
                        <td>{{ $stat['tasks'] }}</td>
                        <td>{{ $stat['rate'] }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        {{ $company }} &bull; {{ __('filament.widgets.report_auto_generated') }}
    </div>
</body>
</html>