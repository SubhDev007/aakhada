<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Round;
use App\Services\GameService;
use Illuminate\Support\Facades\Log;

class StartGameRound extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and start a new game round if needed.';

    /**
     * Execute the console command.
     */
    public function handle(GameService $gameService)
    {
        // 1. Check for Future Round (Pre-booked)
        // If a round exists with start_time > now, we are good.
        $futureRoundExists = Round::where('start_time', '>', now())->exists();
        if ($futureRoundExists) {
             $this->info("Future round already exists. No action needed.");
             return;
        }

        // 2. Check Active Round
        $activeRound = Round::active()->orderBy('end_time', 'desc')->first();

        $shouldCreate = false;

        if (!$activeRound) {
            $shouldCreate = true;
            $this->info("No active round found.");
        } else {
            // Check if active round ends within 2 minutes
            // If cron runs every minute, 2 minutes buffer is safe to ensure no gap.
            if ($activeRound->end_time->diffInMinutes(now()) < 5) {
                $shouldCreate = true;
                $this->info("Active round ends soon ({$activeRound->end_time}). Pre-creating next round.");
            }
        }

        if ($shouldCreate) {
            $this->info("Creating next round...");
            try {
                $newRound = $gameService->createNextRound();
                $this->info("New Round Created: {$newRound->round_serial} (Starts: {$newRound->start_time}, Ends: {$newRound->end_time})");
            } catch (\Exception $e) {
                Log::error("Failed to create next round: " . $e->getMessage());
                $this->error("Failed to create next round.");
            }
        } else {
            $this->info("Round is active and stable.");
        }
    }
}
