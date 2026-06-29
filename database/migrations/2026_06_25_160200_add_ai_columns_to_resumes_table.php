<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->integer('ai_score')->nullable()->after('resume_text');
            $table->longText('ai_summary')->nullable()->after('ai_score');
            $table->longText('ai_report')->nullable()->after('ai_summary');
            $table->string('ai_recommendation')->nullable()->after('ai_report');
            $table->timestamp('analyzed_at')->nullable()->after('ai_recommendation');
        });
    }

    public function down(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn(['ai_score', 'ai_summary', 'ai_report', 'ai_recommendation', 'analyzed_at']);
        });
    }
};
