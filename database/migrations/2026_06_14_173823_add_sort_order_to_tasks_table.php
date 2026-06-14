<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('priority');
        });

        DB::table('tasks')->orderBy('id')->chunk(200, function ($tasks) {
            foreach ($tasks as $index => $task) {
                DB::table('tasks')->where('id', $task->id)->update(['sort_order' => $index]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
