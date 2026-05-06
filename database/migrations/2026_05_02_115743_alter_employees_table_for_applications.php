<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. تعديل حالات الموظف لتشمل قيد المراجعة والمرفوض
        DB::statement("ALTER TABLE employees MODIFY COLUMN status ENUM('pending', 'active', 'on_leave', 'terminated', 'rejected') DEFAULT 'pending'");

        // 2. إضافة حقل سبب الرفض
        Schema::table('employees', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
