<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // من سجل المصروف
            $table->string('title');
            $table->string('category')->nullable(); // مثلاً: رواتب، تشغيل..
            $table->decimal('amount', 12, 2)->unsigned();
            $table->date('expense_date')->nullable();
            $table->string('receipt_url')->nullable(); // صورة الإيصال
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
