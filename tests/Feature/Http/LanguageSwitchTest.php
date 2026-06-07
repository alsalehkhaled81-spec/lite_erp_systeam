<?php

describe('Language Switch', function () {
    test('language can be switched to arabic', function () {
        $this->get('/lang/ar')->assertRedirect();
        expect(session('locale'))->toBe('ar');
    });

    test('language can be switched to english', function () {
        $this->get('/lang/en')->assertRedirect();
        expect(session('locale'))->toBe('en');
    });

    test('invalid locale is ignored', function () {
        $this->get('/lang/fr')->assertRedirect();
        expect(session('locale'))->toBeNull();
    });
});
