<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('receipt_url');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['project_id', 'status', 'approved_by']);
        });
    }
};
