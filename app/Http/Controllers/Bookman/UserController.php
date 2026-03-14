<?php

namespace App\Http\Controllers\Bookman;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $bookmanId = auth()->id();
        $users = User::where('role', 'user')->where('created_by', $bookmanId)->latest()->get();
        return view('bookman.users.index', compact('users'));
    }

    public function create()
    {
        return view('bookman.users.create');
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
            'role' => 'user',
            'wallet_balance' => 0,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('bookman.users.index')->with('success', 'User created successfully.');
    }

    public function addFunds(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $bookman = auth()->user();
        $amount = $request->amount;

        if ($bookman->wallet_balance < $amount) {
            return redirect()->back()->with('error', 'Insufficient funds in your wallet to transfer.');
        }

        // Ensure the Bookman only adds funds to users they created
        if ($user->created_by !== $bookman->id) {
            abort(403, 'Unauthorized access to this user.');
        }

        DB::transaction(function () use ($bookman, $user, $amount, $request) {
            // Deduct from Bookman
            $bookman->decrement('wallet_balance', $amount);
            
            Transaction::create([
                'user_id' => $bookman->id,
                'type' => 'withdrawal',
                'amount' => $amount,
                'balance_after' => $bookman->wallet_balance,
                'description' => 'Transferred funds to user: ' . $user->name,
            ]);

            // Add to User
            $user->increment('wallet_balance', $amount);

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'deposit',
                'amount' => $amount,
                'balance_after' => $user->wallet_balance,
                'description' => $request->description ?? 'Funds added by Bookman',
            ]);
        });

        return redirect()->route('bookman.users.index')->with('success', 'Funds added successfully.');
    }
}
