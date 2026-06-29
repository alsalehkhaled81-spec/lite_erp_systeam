<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->longText('requirements')->nullable();
            $table->string('location')->nullable();
            $table->string('employment_type')->default('full_time');
            $table->decimal('salary_min', 10, 2)->unsigned()->nullable();
            $table->decimal('salary_max', 10, 2)->unsigned()->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('open');
            $table->integer('positions_count')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
