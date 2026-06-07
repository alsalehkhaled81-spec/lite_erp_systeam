<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('housing_allowance', 10, 2)->default(0)->after('deductions');
            $table->decimal('transport_allowance', 10, 2)->default(0)->after('housing_allowance');
            $table->decimal('phone_allowance', 10, 2)->default(0)->after('transport_allowance');
            $table->decimal('social_insurance_rate', 5, 2)->default(0)->after('phone_allowance');
            $table->decimal('social_insurance_amount', 10, 2)->default(0)->after('social_insurance_rate');
            $table->integer('absence_days')->default(0)->after('social_insurance_amount');
            $table->decimal('absence_deduction', 10, 2)->default(0)->after('absence_days');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'housing_allowance', 'transport_allowance', 'phone_allowance',
                'social_insurance_rate', 'social_insurance_amount',
                'absence_days', 'absence_deduction',
            ]);
        });
    }
};
