<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
        $this->info('Generating today\'s rounds from schedule templates...');

        try {
            $created = $gameService->generateTodaysRounds();

            if (empty($created)) {
                $this->info('All scheduled rounds for today already exist or none are configured.');
            } else {
                foreach ($created as $round) {
                    $this->info("Created round: [{$round->name}] {$round->round_serial} ({$round->start_time} → {$round->end_time})");
                }
                $this->info(count($created) . ' round(s) created successfully.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to generate today\'s rounds: ' . $e->getMessage());
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
