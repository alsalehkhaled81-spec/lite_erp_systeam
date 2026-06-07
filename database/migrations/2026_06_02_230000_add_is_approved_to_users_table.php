<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('profile_photo_path');
        });

        \App\Models\User::whereHas('role', function ($q) {
            $q->where('name', 'super_admin');
        })->update(['is_approved' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};
