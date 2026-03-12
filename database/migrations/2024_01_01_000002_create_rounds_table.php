<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rounds', function (Blueprint $table) {
            $table->id();
            $table->string('round_serial')->index(); // e.g. 20231027-R1
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('result_number')->nullable(); // 0-9
            $table->enum('status', ['active', 'processing', 'completed', 'cancelled'])->default('active');
            $table->decimal('total_pool', 15, 2)->default(0);
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rounds');
    }
};
