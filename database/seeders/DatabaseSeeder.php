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

        // 4. Default Round Schedules
        $schedules = [
            ['name' => 'Morning Special', 'start_time' => '10:00:00', 'duration_minutes' => 60],
            ['name' => 'Afternoon Classic', 'start_time' => '14:00:00', 'duration_minutes' => 60],
            ['name' => 'Evening Rush', 'start_time' => '18:00:00', 'duration_minutes' => 60],
            ['name' => 'Night Owl', 'start_time' => '22:00:00', 'duration_minutes' => 120],
        ];

        foreach ($schedules as $s) {
            \App\Models\RoundSchedule::firstOrCreate(['name' => $s['name']], $s);
        }

        // 5. Generate rounds for today
        $service = new GameService();
        $service->generateTodaysRounds();
    }
}
