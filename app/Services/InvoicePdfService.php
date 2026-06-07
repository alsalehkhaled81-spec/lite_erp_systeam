<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfService
{
    public function generate(Invoice $invoice)
    {
        $invoice->load(['client', 'project', 'items']);

        $arabic = new \Arphp\Glyphs();

        $clientName = $arabic->utf8Glyphs($invoice->client->name);
        $companyName = $invoice->client->company_name ? $arabic->utf8Glyphs($invoice->client->company_name) : null;
        
        $invoice->client->name = $clientName;
        $invoice->client->company_name = $companyName;

        foreach ($invoice->items as $item) {
            $item->description = $arabic->utf8Glyphs($item->description);
        }

        $company = $arabic->utf8Glyphs(config('app.name', 'ERP-Lite'));

        $data = [
            'invoice' => $invoice,
            'client' => $invoice->client,
            'items' => $invoice->items,
            'company' => $company,
        ];

        $pdf = Pdf::loadView('pdf.invoice', $data);

        $filename = 'invoice_' . $invoice->invoice_number . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}
