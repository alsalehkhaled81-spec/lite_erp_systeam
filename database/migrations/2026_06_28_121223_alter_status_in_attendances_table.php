<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('present', 'late', 'absent', 'half_day', 'over_time') NOT NULL");
        } else {
            Schema::table('attendances', function (Blueprint $table) {
                $table->string('status')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('present', 'late', 'absent', 'half_day') NOT NULL");
        } else {
            Schema::table('attendances', function (Blueprint $table) {
                $table->string('status')->change();
            });
        }
    }
};
