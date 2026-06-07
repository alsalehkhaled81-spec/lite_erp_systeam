<?php

use App\Models\Skill;
use App\Models\Employee;

describe('Skill Model', function () {
    test('skill has correct fillable attributes', function () {
        $skill = new Skill();
        expect($skill->getFillable())->toContain('name');
    });

    test('skill belongs to many employees', function () {
        $skill = Skill::factory()->create();
        $employees = Employee::factory()->count(3)->create();
        $skill->employees()->attach($employees->pluck('id'));
        expect($skill->employees)->toHaveCount(3);
    });
});
