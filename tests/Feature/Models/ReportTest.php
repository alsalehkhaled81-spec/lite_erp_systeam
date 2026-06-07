<?php

use App\Models\Report;
use App\Models\Employee;

describe('Report Model', function () {
    test('report has correct fillable attributes', function () {
        $report = new Report();
        expect($report->getFillable())->toContain(
            'sender_id', 'receiver_id', 'title', 'content', 'feedback', 'status'
        );
    });

    test('report belongs to sender employee', function () {
        $sender = Employee::factory()->create();
        $report = Report::factory()->create(['sender_id' => $sender->id]);
        expect($report->sender)->not->toBeNull()->and($report->sender->id)->toBe($sender->id);
    });

    test('report belongs to receiver employee', function () {
        $receiver = Employee::factory()->create();
        $report = Report::factory()->create(['receiver_id' => $receiver->id]);
        expect($report->receiver)->not->toBeNull()->and($report->receiver->id)->toBe($receiver->id);
    });

    test('report can have different sender and receiver', function () {
        $sender = Employee::factory()->create();
        $receiver = Employee::factory()->create();
        $report = Report::factory()->create(['sender_id' => $sender->id, 'receiver_id' => $receiver->id]);
        expect($report->sender->id)->toBe($sender->id)
            ->and($report->receiver->id)->toBe($receiver->id)
            ->and($report->sender->id)->not->toBe($report->receiver->id);
    });
});
