<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('vacancy_id')->nullable()->after('department_id')
                ->constrained('vacancies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['vacancy_id']);
            $table->dropColumn('vacancy_id');
        });
    }
};
