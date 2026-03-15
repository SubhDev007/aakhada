<?php

namespace App\Http\Controllers\Bookman;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $bookman    = auth()->user();
        $date       = $request->get('date', today()->toDateString());
        $reportDate = Carbon::parse($date);

        // All users this bookman manages
        $users   = User::where('created_by', $bookman->id)->get();
        $userIds = $users->pluck('id');

        $userReports = [];

        foreach ($users as $user) {
            $bets = Bet::where('user_id', $user->id)
                ->whereDate('created_at', $reportDate)
                ->get();

            $totalDeposited = $bets->sum('gross_amount');
            $totalWon       = $bets->where('status', 'won')->sum('winnings');
            $totalLost      = $bets->where('status', 'lost')->sum('gross_amount');

            $userReports[] = [
                'user'            => $user,
                'bet_count'       => $bets->count(),
                'total_deposited' => $totalDeposited,
                'total_won'       => $totalWon,
                'total_lost'      => $totalLost,
                'net'             => $totalDeposited - $totalWon, // bookman keeps this
            ];
        }

        // Bookman summary
        $totalCollected  = collect($userReports)->sum('total_deposited');
        $totalWinnings   = collect($userReports)->sum('total_won');
        $commissionRate  = 5; // 5%
        $myCommission    = ($totalCollected * $commissionRate) / 100;
        $toSendAdmin     = $totalCollected;                         // evening: send to admin
        $toReceiveMorning = $totalWinnings + $myCommission;         // morning: receive from admin

        return view('bookman.report', compact(
            'userReports',
            'reportDate',
            'date',
            'totalCollected',
            'totalWinnings',
            'myCommission',
            'toSendAdmin',
            'toReceiveMorning',
            'commissionRate'
        ));
    }
}
