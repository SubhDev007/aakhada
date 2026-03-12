<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Round;
use App\Services\GameService;
use Illuminate\Support\Facades\Log;

class ProcessGameRound extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:calculate-result';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for ended rounds, calculate results, and distribute winnings.';

    /**
     * Execute the console command.
     */
    public function handle(GameService $gameService)
    {
        $this->info("Checking for rounds to process...");

        // 1. Process Ended Rounds
        // Find active rounds where end_time has passed
        $roundsToProcess = Round::where('status', 'active')
            ->where('end_time', '<=', now())
            ->get();

        foreach ($roundsToProcess as $round) {
            $this->info("Processing Round: {$round->round_serial}");
            try {
                // Calculate Result
                $winningNumber = $gameService->calculateResult($round);
                $this->info("Result: Number {$winningNumber}");

                // Distribute Winnings
                $gameService->distributeWinnings($round);
                $this->info("Winnings distributed.");

            } catch (\Exception $e) {
                Log::error("Failed to process round {$round->id}: " . $e->getMessage());
                $this->error("Error processing round {$round->id}");
            }
        }

        $this->info("Result calculation check complete.");
    }
}
