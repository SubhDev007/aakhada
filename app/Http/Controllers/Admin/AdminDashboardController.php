<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\Round;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_bets' => Bet::count(),
            'total_stake' => Bet::sum('gross_amount'),
            'active_round' => Round::active()->first(),
            'pending_bets' => Bet::where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function monitoring()
    {
        $round = Round::active()->first();

        $stats = [];
        if ($round) {
            $stats = Bet::where('round_id', $round->id)
                ->selectRaw('chosen_number, SUM(net_amount) as total_stake, COUNT(*) as bet_count')
                ->groupBy('chosen_number')
                ->get()
                ->keyBy('chosen_number')
                ->toArray();
        }

        // Ensure all numbers 0-9 are represented
        $formattedStats = [];
        for ($i = 0; $i <= 9; $i++) {
            $formattedStats[$i] = [
                'total_stake' => $stats[$i]['total_stake'] ?? 0,
                'bet_count' => $stats[$i]['bet_count'] ?? 0,
            ];
        }

        if (request()->ajax()) {
            return response()->json([
                'round' => $round,
                'stats' => $formattedStats
            ]);
        }

        return view('admin.monitoring', compact('round', 'formattedStats'));
    }

    public function bets()
    {
        $bets = Bet::with(['user', 'round'])->latest()->paginate(50);
        return view('admin.bets.index', compact('bets'));
    }

    public function settings()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.edit', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
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

        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
