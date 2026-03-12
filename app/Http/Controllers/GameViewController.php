<?php

namespace App\Http\Controllers;

use App\Services\GameService;
use App\Models\Round;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameViewController extends Controller
{
    public function index()
    {
        $round = Round::active()->first();
        $activeBets = collect([]);

        if (Auth::check() && $round) {
            $activeBets = Auth::user()->bets()
                ->where('round_id', $round->id)
                ->where('status', 'pending')
                ->latest()
                ->get();
        }

        $noBetBufferSeconds = \App\Models\Setting::getValue('no_bet_buffer_minutes', 10) * 60;

        $lastRound = Round::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->first();

        $pastRounds = Round::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $betHistory = collect([]);

        if (Auth::check()) {
            if ($round) {
                $activeBets = Auth::user()->bets()
                    ->where('round_id', $round->id)
                    ->where('status', 'pending')
                    ->latest()
                    ->get();
            }

            $betHistory = Auth::user()->bets()
                ->where('status', '!=', 'pending')
                ->latest()
                ->limit(20)
                ->get();
        }

        return view('game.index', compact('round', 'activeBets', 'noBetBufferSeconds', 'lastRound', 'pastRounds', 'betHistory'));
    }
}
