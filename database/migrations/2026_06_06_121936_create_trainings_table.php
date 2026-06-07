<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('trainer')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['upcoming', 'ongoing', 'completed'])->default('upcoming');
            $table->string('location')->nullable();
            $table->integer('max_participants')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
