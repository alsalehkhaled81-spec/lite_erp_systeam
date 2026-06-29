<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('password');
            $table->rememberToken()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['password', 'is_active', 'remember_token']);
        });
    }
};
