<x-filament-widgets::widget>
    <div class="fi-section rounded-xl bg-white dark:bg-gray-900 shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="fi-section-header flex items-center gap-2 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30">
                <x-heroicon-o-calculator class="w-4 h-4 text-amber-600 dark:text-amber-400"/>
            </span>
            <h2 class="text-base font-bold text-gray-800 dark:text-gray-100">{{ __('filament.widgets.tax_report') }}</h2>
        </div>
        <div class="overflow-hidden">
            <table class="w-full text-sm">
                <tbody>
                    @php
                        $rows = [
                            ['label' => __('filament.widgets.ytd_revenue'), 'value' => $ytdRevenue, 'type' => 'default'],
                            ['label' => __('filament.widgets.ytd_expenses'), 'value' => $ytdExpenses, 'type' => 'default'],
                            ['label' => __('filament.widgets.gross_profit'), 'value' => $grossProfit, 'type' => $grossProfit >= 0 ? 'success' : 'danger'],
                            ['label' => __('filament.widgets.salaries_paid'), 'value' => $ytdSalaries, 'type' => 'default'],
                            ['label' => __('filament.widgets.taxable_income'), 'value' => $taxableIncome, 'type' => 'default'],
                            ['label' => __('filament.widgets.estimated_tax') . ' @ ' . $taxRate . '%', 'value' => $estimatedTax, 'type' => 'warning'],
                            ['label' => __('filament.widgets.net_after_tax'), 'value' => $netAfterTax, 'type' => $netAfterTax >= 0 ? 'success' : 'danger'],
                            ['label' => __('filament.widgets.current_quarter_revenue'), 'value' => $quarterRevenue, 'type' => 'default'],
                            ['label' => __('filament.widgets.quarter_expenses'), 'value' => $quarterExpenses, 'type' => 'default'],
                        ];
                        $index = 0;
                    @endphp
                    @foreach($rows as $row)
                        @php
                            $index++;
                            $isEven = $index % 2 === 0;
                            $bgClass = match($row['type']) {
                                'warning' => 'bg-red-50 dark:bg-red-900/20',
                                'success' => ($row['value'] >= 0 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20'),
                                'danger' => 'bg-red-50 dark:bg-red-900/20',
                                default => ($isEven ? 'bg-gray-50 dark:bg-gray-800/40' : 'bg-white dark:bg-gray-900'),
                            };
                            $textClass = match($row['type']) {
                                'warning' => 'text-red-700 dark:text-red-300',
                                'success' => ($row['value'] >= 0 ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300'),
                                'danger' => 'text-red-700 dark:text-red-300',
                                default => 'text-gray-800 dark:text-gray-200',
                            };
                            $labelClass = match($row['type']) {
                                'warning' => 'text-red-700 dark:text-red-300 font-semibold',
                                'success' => ($row['value'] >= 0 ? 'text-green-700 dark:text-green-300 font-semibold' : 'text-red-700 dark:text-red-300 font-semibold'),
                                'danger' => 'text-red-700 dark:text-red-300 font-semibold',
                                default => 'text-gray-600 dark:text-gray-400',
                            };
                        @endphp
                        <tr class="{{ $bgClass }} border-b border-gray-100 dark:border-gray-800 last:border-b-0">
                            <td class="px-6 py-3 {{ $labelClass }}">{{ $row['label'] }}</td>
                            <td class="px-6 py-3 text-left {{ $textClass }} font-mono font-medium">
                                ${{ number_format((float) $row['value'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-widgets::widget>
