<?php

namespace App\Http\Controllers\Bookman;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $stats = [
            'wallet_balance' => $user->wallet_balance,
            'total_users_created' => User::where('created_by', $user->id)->count(), // We need to add created_by to users
        ];

        return view('bookman.dashboard', compact('stats'));
    }
}
