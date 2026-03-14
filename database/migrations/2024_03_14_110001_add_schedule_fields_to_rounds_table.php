<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rounds', function (Blueprint $table) {
            $table->string('name')->nullable()->after('round_serial'); // e.g. "Morning Gold"
            $table->foreignId('round_schedule_id')->nullable()->constrained('round_schedules')->nullOnDelete()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('rounds', function (Blueprint $table) {
            $table->dropForeign(['round_schedule_id']);
            $table->dropColumn(['name', 'round_schedule_id']);
        });
    }
};
