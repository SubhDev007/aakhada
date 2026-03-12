<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BetController extends Controller
{
    protected $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function place(Request $request)
    {
        $request->validate([
            'number' => 'required|integer|min:0|max:9',
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            $bet = $this->gameService->placeBet(Auth::user(), $request->number, $request->amount);
            return response()->json(['message' => 'Bet placed successfully', 'bet' => $bet]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function history()
    {
        $bets = Auth::user()->bets()->with('round')->latest()->paginate(20);
        return response()->json($bets);
    }
}
