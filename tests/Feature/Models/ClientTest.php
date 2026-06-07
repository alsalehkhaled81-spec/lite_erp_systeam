<?php

use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;

describe('Client Model', function () {
    test('client has correct fillable attributes', function () {
        $client = new Client();
        expect($client->getFillable())->toContain(
            'name', 'company_name', 'email', 'phone', 'address'
        );
    });

    test('client has many projects', function () {
        $client = Client::factory()->create();
        Project::factory()->count(3)->create(['client_id' => $client->id]);
        expect($client->projects)->toHaveCount(3);
    });

    test('client has many invoices', function () {
        $client = Client::factory()->create();
        Invoice::factory()->count(2)->create(['client_id' => $client->id]);
        expect($client->invoices)->toHaveCount(2);
    });
});
