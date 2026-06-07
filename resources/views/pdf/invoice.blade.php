<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 40px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .company-name { font-size: 24px; font-weight: bold; color: #1a56db; }
        .invoice-title { font-size: 20px; font-weight: bold; color: #666; text-align: right; }
        .section { margin-bottom: 30px; }
        .section-title { font-size: 14px; font-weight: bold; color: #1a56db; border-bottom: 2px solid #1a56db; padding-bottom: 5px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f0f4ff; padding: 8px; text-align: left; border: 1px solid #ddd; }
        td { padding: 8px; border: 1px solid #ddd; }
        .totals { margin-top: 20px; text-align: right; }
        .totals td { border: none; padding: 4px 8px; }
        .grand-total { font-size: 16px; font-weight: bold; color: #1a56db; }
        .info-grid { display: flex; gap: 40px; }
        .info-grid div { flex: 1; }
        .label { color: #666; font-size: 11px; }
        .value { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="company-name">{{ $company }}</div>
            <div style="color:#666;">Invoice</div>
        </div>
        <div>
            <div class="invoice-title">INV-{{ $invoice->invoice_number }}</div>
            <div style="color:#666; text-align:right;">{{ $invoice->issue_date ? $invoice->issue_date->format('M d, Y') : '-' }}</div>
        </div>
    </div>

    <div class="section">
        <div class="info-grid">
            <div>
                <div class="section-title">Bill To</div>
                <div class="value">{{ $client->name }}</div>
                @if($client->company_name)<div>{{ $client->company_name }}</div>@endif
                <div>{{ $client->email }}</div>
                <div>{{ $client->phone }}</div>
            </div>
            <div>
                <div class="section-title">Invoice Details</div>
                <div><span class="label">Invoice #:</span> {{ $invoice->invoice_number }}</div>
                <div><span class="label">Issue Date:</span> {{ $invoice->issue_date ? $invoice->issue_date->format('M d, Y') : '-' }}</div>
                <div><span class="label">Due Date:</span> {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '-' }}</div>
                <div><span class="label">Status:</span> {{ strtoupper($invoice->status) }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Items</div>
        <table>
            <thead>
                <tr>
                    <th style="width:50%">Description</th>
                    <th style="width:15%">Qty</th>
                    <th style="width:15%">Unit Price</th>
                    <th style="width:20%">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
                @if($items->isEmpty())
                <tr>
                    <td colspan="3">Total Amount</td>
                    <td>${{ number_format($invoice->amount, 2) }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="totals">
        <table style="width:300px; margin-left:auto;">
            <tr><td>Subtotal:</td><td style="text-align:right">${{ number_format($invoice->amount, 2) }}</td></tr>
            @if($invoice->vat_rate > 0)
            <tr><td>VAT ({{ $invoice->vat_rate }}%):</td><td style="text-align:right">${{ number_format($invoice->vat_amount, 2) }}</td></tr>
            @endif
            <tr class="grand-total"><td>Total:</td><td style="text-align:right">${{ number_format($invoice->total_with_vat, 2) }}</td></tr>
        </table>
    </div>
</body>
</html>
