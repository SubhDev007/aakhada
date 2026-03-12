<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use App\Services\GameService;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Superadmin
        if (!User::where('email', 'admin@akhada.com')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@akhada.com',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'wallet_balance' => 1000000,
            ]);
        }

        // 2. Create Default User
        if (!User::where('email', 'player@akhada.com')->exists()) {
            User::create([
                'name' => 'Player One',
                'email' => 'player@akhada.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'wallet_balance' => 1000,
            ]);
        }

        // 3. Default Settings
        $defaults = [
            'min_bet' => 10,
            'max_bet' => 10000,
            'platform_fee_percent' => 15,
            'round_duration_minutes' => 1,
            'no_bet_buffer_minutes' => 0.0833, // 5 seconds
            'default_result_logic' => 'lowest_pool',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        // 4. Ensure an active round exists
        $service = new GameService();
        if (!\App\Models\Round::active()->exists()) {
            $service->createNextRound();
        }
    }
}
