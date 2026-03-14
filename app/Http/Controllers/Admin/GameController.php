<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Round;
use App\Models\Setting;
use App\Services\GameService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    protected $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'min_bet' => 'required|numeric',
                'max_bet' => 'required|numeric',
                'platform_fee_percent' => 'required|numeric',
                'round_duration_minutes' => 'required|numeric',
                'no_bet_buffer_minutes' => 'required|numeric',
                'default_result_logic' => 'required|in:lowest_pool,average,highest_pool',
            ]);

            foreach ($data as $key => $value) {
                Setting::setValue($key, $value);
            }

            return response()->json(['message' => 'Settings updated successfully']);
        }

        return response()->json([
            'settings' => Setting::all()->pluck('value', 'key'),
        ]);
    }

    public function currentRound()
    {
        $round = Round::active()->with(['bets'])->first();
        if (!$round) {
            return response()->json(['message' => 'No active round'], 404);
        }

        $stats = $round->bets()
            ->selectRaw('chosen_number, SUM(net_amount) as total_stake, COUNT(*) as bet_count')
            ->groupBy('chosen_number')
            ->get();

        return response()->json([
            'round' => $round,
            'stats' => $stats,
            'settings' => [
                'no_bet_buffer_seconds' => Setting::getValue('no_bet_buffer_minutes', 10) * 60
            ]
        ]);
    }

    public function setResult(Request $request)
    {
        $request->validate([
            'round_id' => 'required|exists:rounds,id',
            'winning_number' => 'required|integer|min:0|max:9',
        ]);

        $round = Round::findOrFail($request->round_id);

        // Admin locks the result
        $round->result_number = $request->winning_number;
        $round->save();

        return response()->json(['message' => 'Winning number set successfully. Result will be processed at round end.']);
    }

    public function processManually()
    {
        // Calculate result for ended rounds
        \Illuminate\Support\Facades\Artisan::call('game:calculate-result');
        // Generate today's scheduled rounds
        \Illuminate\Support\Facades\Artisan::call('game:start');

        return response()->json(['message' => 'Game process triggered successfully.', 'output' => \Illuminate\Support\Facades\Artisan::output()]);
    }
}
