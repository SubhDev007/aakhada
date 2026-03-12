<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('round_id')->constrained()->cascadeOnDelete();
            $table->integer('chosen_number'); // 0-9
            $table->decimal('gross_amount', 15, 2);
            $table->decimal('fee_amount', 15, 2);
            $table->decimal('net_amount', 15, 2); // Active stake
            $table->enum('status', ['pending', 'won', 'lost', 'refunded'])->default('pending');
            $table->decimal('winnings', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['round_id', 'chosen_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bets');
    }
};
