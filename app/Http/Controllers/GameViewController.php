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
        // Currently active round (start_time <= now AND end_time > now)
        $round = Round::active()->first();

        // If no active round, find the next upcoming round for today
        $nextRound = null;
        if (!$round) {
            $nextRound = Round::where('status', 'active')
                ->where('start_time', '>', now())
                ->orderBy('start_time')
                ->first();
        }

        $activeBets = collect([]);
        $betHistory = collect([]);

        if (Auth::check() && $round) {
            $activeBets = Auth::user()->bets()
                ->where('round_id', $round->id)
                ->where('status', 'pending')
                ->latest()
                ->get();
        }

        if (Auth::check()) {
            $betHistory = Auth::user()->bets()
                ->where('status', '!=', 'pending')
                ->with('round')
                ->latest()
                ->limit(20)
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

        return view('game.index', compact('round', 'nextRound', 'activeBets', 'noBetBufferSeconds', 'lastRound', 'pastRounds', 'betHistory'));
    }
}
