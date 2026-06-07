<?php

use App\Models\Expense;
use App\Models\User;

describe('Expense Model', function () {
    test('expense has correct fillable attributes', function () {
        $expense = new Expense();
        expect($expense->getFillable())->toContain(
            'user_id', 'title', 'category', 'amount', 'expense_date', 'receipt_url'
        );
    });

    test('expense belongs to user', function () {
        $user = User::factory()->create();
        $expense = Expense::factory()->create(['user_id' => $user->id]);
        expect($expense->user)->not->toBeNull()->and($expense->user->id)->toBe($user->id);
    });
});
