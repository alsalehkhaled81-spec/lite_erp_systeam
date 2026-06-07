<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('current_role');
            $table->string('target_role');
            $table->integer('timeline_months');
            $table->text('required_skills')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_plans');
    }
};
