<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class BookmanController extends Controller
{
    public function index()
    {
        $bookmen = User::where('role', 'bookman')->latest()->get();
        return view('admin.bookmen.index', compact('bookmen'));
    }

    public function create()
    {
        return view('admin.bookmen.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'bookman', // strictly for creating bookmen
            'wallet_balance' => 0,
        ]);

        return redirect()->route('admin.bookmen.index')->with('success', 'Bookman created successfully.');
    }

    public function addFunds(Request $request, User $bookman)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $bookman) {
            $amount = $request->amount;
            $bookman->increment('wallet_balance', $amount);

            Transaction::create([
                'user_id' => $bookman->id,
                'type' => 'deposit',
                'amount' => $amount,
                'balance_after' => $bookman->wallet_balance,
                'description' => $request->description ?? 'Admin fund addition to bookman',
            ]);
        });

        return redirect()->route('admin.bookmen.index')->with('success', 'Funds added successfully.');
    }
}
