<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function index(Request $request)
    {
        // Default to today, allow date filter
        $date = $request->get('date', today()->toDateString());
        $reportDate = Carbon::parse($date);

        // Get all Bookmen
        $bookmen = User::where('role', 'bookman')->get();

        $settlements = [];

        foreach ($bookmen as $bookman) {
            // Get all users created by this bookman
            $userIds = User::where('created_by', $bookman->id)->pluck('id');

            if ($userIds->isEmpty()) {
                $settlements[] = [
                    'bookman'           => $bookman,
                    'total_deposits'    => 0,
                    'total_bets'        => 0,
                    'total_winnings'    => 0,
                    'platform_fee'      => 0,
                    'net_to_pay_back'   => 0,
                    'platform_profit'   => 0,
                    'bet_count'         => 0,
                ];
                continue;
            }

            // Bets placed today by this bookman's users
            $bets = Bet::whereIn('user_id', $userIds)
                ->whereDate('created_at', $reportDate)
                ->get();

            $totalBets      = $bets->sum('gross_amount');   // total cash collected from users
            $totalFees      = $bets->sum('fee_amount');      // platform commission already earned
            $totalWinnings  = $bets->where('status', 'won')->sum('winnings'); // what users won

            // Net to send back to Bookman next morning:
            // = User winnings (Bookman must pay users) + Bookman's commission
            $bookmanCommissionRate = 5; // 5% — make this configurable later
            $bookmanCommission     = ($totalBets * $bookmanCommissionRate) / 100;
            $netToPayBack          = $totalWinnings + $bookmanCommission;

            // Platform profit from this bookman's network today
            $platformProfit = $totalBets - $totalWinnings - $bookmanCommission;

            $settlements[] = [
                'bookman'           => $bookman,
                'total_deposits'    => $totalBets,       // what bookman should send admin tonight
                'total_bets'        => $totalBets,
                'total_winnings'    => $totalWinnings,   // users' winnings
                'platform_fee'      => $totalFees,
                'bookman_commission'=> $bookmanCommission,
                'net_to_pay_back'   => $netToPayBack,    // admin sends this to bookman next morning
                'platform_profit'   => $platformProfit,
                'bet_count'         => $bets->count(),
            ];
        }

        $grandTotalDeposits   = collect($settlements)->sum('total_deposits');
        $grandTotalWinnings   = collect($settlements)->sum('total_winnings');
        $grandTotalCommission = collect($settlements)->sum('bookman_commission');
        $grandNetPayBack      = collect($settlements)->sum('net_to_pay_back');
        $grandProfit          = collect($settlements)->sum('platform_profit');

        return view('admin.settlements.index', compact(
            'settlements',
            'reportDate',
            'date',
            'grandTotalDeposits',
            'grandTotalWinnings',
            'grandTotalCommission',
            'grandNetPayBack',
            'grandProfit'
        ));
    }
}
