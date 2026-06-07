<?php

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Project;

describe('Invoice Model', function () {
    test('invoice has correct fillable attributes', function () {
        $invoice = new Invoice();
        expect($invoice->getFillable())->toContain(
            'client_id', 'project_id', 'invoice_number', 'amount', 'issue_date', 'due_date', 'status'
        );
    });

    test('invoice belongs to client', function () {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create(['client_id' => $client->id]);
        expect($invoice->client)->not->toBeNull()->and($invoice->client->id)->toBe($client->id);
    });

    test('invoice belongs to project', function () {
        $project = Project::factory()->create();
        $invoice = Invoice::factory()->create(['project_id' => $project->id]);
        expect($invoice->project)->not->toBeNull()->and($invoice->project->id)->toBe($project->id);
    });
});
