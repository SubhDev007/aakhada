<?php

namespace App\Services;

use App\Models\Bet;
use App\Models\Round;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class GameService
{
    public function placeBet(User $user, $number, $amount)
    {
        $minBet = Setting::getValue('min_bet', 10);
        $maxBet = Setting::getValue('max_bet', 10000);
        $platformFeePercent = Setting::getValue('platform_fee_percent', 15);

        if ($amount < $minBet || $amount > $maxBet) {
            throw new Exception("Bet amount must be between {$minBet} and {$maxBet}");
        }

        if ($user->wallet_balance < $amount) {
            throw new Exception("Insufficient wallet balance");
        }

        $currentRound = Round::active()->first();
        if (!$currentRound) {
            throw new Exception("No active betting round");
        }

        // Check No-Bet Zone
        $noBetBuffer = Setting::getValue('no_bet_buffer_minutes', 10);
        if (now()->greaterThanOrEqualTo($currentRound->end_time->subMinutes($noBetBuffer))) {
            throw new Exception("Betting is closed for this round");
        }

        return DB::transaction(function () use ($user, $currentRound, $number, $amount, $platformFeePercent) {
            // Deduct Wallet
            $user->wallet_balance -= $amount;
            $user->save();

            // Create Transaction
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'bet_placed',
                'amount' => -$amount,
                'balance_after' => $user->wallet_balance,
                'description' => "Bet on Number {$number} for Round {$currentRound->round_serial}",
            ]);

            // Calculate Fee
            $feeAmount = floor(($amount * ($platformFeePercent / 100)) * 100) / 100; // Round down to 2 decimals
            $netAmount = $amount - $feeAmount;

            // Create Bet
            $bet = Bet::create([
                'user_id' => $user->id,
                'round_id' => $currentRound->id,
                'chosen_number' => $number,
                'gross_amount' => $amount,
                'fee_amount' => $feeAmount,
                'net_amount' => $netAmount,
                'status' => 'pending',
            ]);

            // Update Round Stats
            $currentRound->increment('total_pool', $amount);
            $currentRound->increment('commission_amount', $feeAmount);

            return $bet;
        });
    }

    public function calculateResult(Round $round)
    {
        if ($round->status !== 'active' && $round->status !== 'processing') {
            return;
        }

        // 1. Check if Admin manually set a result (already in DB?)
        // If result_number is not null, use it.
        // Otherwise calculate it.
        $winningNumber = $round->result_number;

        if (is_null($winningNumber)) {
            $logic = Setting::getValue('default_result_logic', 'lowest_pool'); // lowest_pool, average, highest_pool

            // Calculate pools per number
            $pools = Bet::where('round_id', $round->id)
                ->select('chosen_number', DB::raw('SUM(net_amount) as total_stake'))
                ->groupBy('chosen_number')
                ->pluck('total_stake', 'chosen_number')
                ->toArray();

            // Ensure all numbers 0-9 exist
            for ($i = 0; $i <= 9; $i++) {
                if (!isset($pools[$i]))
                    $pools[$i] = 0;
            }

            if ($logic === 'lowest_pool') {
                // Find number with minimum stake. If tie, pick random.
                $minStake = min($pools);
                $candidates = array_keys($pools, $minStake);
                $winningNumber = $candidates[array_rand($candidates)];
            } elseif ($logic === 'highest_pool') {
                $maxStake = max($pools);
                $candidates = array_keys($pools, $maxStake);
                $winningNumber = $candidates[array_rand($candidates)];
            } else {
                // Average logic - simplified (random for now as "Average" is ambiguous without specific detailed algo)
                // User said "Medium (True Average): The number with the middle-tier amount of bets."
                asort($pools);
                $keys = array_keys($pools);
                $middleIndex = floor(count($keys) / 2);
                $winningNumber = $keys[$middleIndex];
            }
        }

        $round->result_number = $winningNumber;
        $round->status = 'processing';
        $round->save();

        return $winningNumber;
    }

    public function distributeWinnings(Round $round)
    {
        $winningNumber = $round->result_number;
        if (is_null($winningNumber))
            return;

        DB::transaction(function () use ($round, $winningNumber) {
            $winningBets = Bet::where('round_id', $round->id)
                ->where('chosen_number', $winningNumber)
                ->where('status', 'pending')
                ->get();

            foreach ($winningBets as $bet) {
                $winnings = $bet->net_amount * 2;

                $bet->status = 'won';
                $bet->winnings = $winnings;
                $bet->save();

                $user = $bet->user;
                $user->wallet_balance += $winnings;
                $user->save();

                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'bet_win',
                    'amount' => $winnings,
                    'balance_after' => $user->wallet_balance,
                    'reference_id' => $bet->id,
                    'description' => "Won Bet #{$bet->id} on Number {$winningNumber}",
                ]);
            }

            // Mark losing bets
            Bet::where('round_id', $round->id)
                ->where('chosen_number', '!=', $winningNumber)
                ->where('status', 'pending')
                ->update(['status' => 'lost']);

            $round->status = 'completed';
            $round->save();
        });
    }

    public function createNextRound()
    {
        // Logic to create next round based on schedule
        $duration = (float) Setting::getValue('round_duration_minutes', 180); // 3 hours default

        $lastRound = Round::latest('end_time')->first();
        $startTime = $lastRound ? $lastRound->end_time : now();
        // If last round ended in the past (gap), start now
        if ($startTime->lessThan(now())) {
            $startTime = now();
        }

        // Align to minutes if needed? Keeping it simple.

        $endTime = $startTime->copy()->addMinutes($duration);
        $serial = $startTime->format('Ymd') . '-R' . (Round::whereDate('start_time', $startTime->toDateString())->count() + 1);

        return Round::create([
            'round_serial' => $serial,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'active'
        ]);
    }
}
