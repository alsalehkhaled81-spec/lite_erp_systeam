<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('month_year');
            $table->decimal('basic_salary', 10, 2)->unsigned();
            $table->decimal('bonuses', 10, 2)->unsigned()->default(0);
            $table->decimal('deductions', 10, 2)->unsigned()->default(0);
            $table->decimal('net_salary', 10, 2)->unsigned();
            $table->string('status')->default('unpaid');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
