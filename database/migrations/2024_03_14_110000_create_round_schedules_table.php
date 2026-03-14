<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('round_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. "Morning Gold"
            $table->time('start_time');       // e.g. "10:00:00"
            $table->integer('duration_minutes'); // e.g. 15
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('round_schedules');
    }
};
