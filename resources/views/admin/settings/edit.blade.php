@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Game Settings</h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card p-4">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Minimum Bet (₹)</label>
                            <input type="number" name="min_bet" class="form-control"
                                value="{{ $settings['min_bet'] ?? 10 }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Maximum Bet (₹)</label>
                            <input type="number" name="max_bet" class="form-control"
                                value="{{ $settings['max_bet'] ?? 10000 }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Platform Fee (%)</label>
                            <input type="number" step="0.1" name="platform_fee_percent" class="form-control"
                                value="{{ $settings['platform_fee_percent'] ?? 15 }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Round Duration (Minutes)</label>
                            <input type="number" name="round_duration_minutes" class="form-control"
                                value="{{ $settings['round_duration_minutes'] ?? 180 }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">No-Bet Buffer (Minutes before end)</label>
                            <input type="number" name="no_bet_buffer_minutes" class="form-control"
                                value="{{ $settings['no_bet_buffer_minutes'] ?? 10 }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Default Winning Logic</label>
                            <select name="default_result_logic" class="form-select">
                                <option value="lowest_pool" {{ ($settings['default_result_logic'] ?? '') == 'lowest_pool' ? 'selected' : '' }}>Lowest Pool (House Wins More)</option>
                                <option value="average" {{ ($settings['default_result_logic'] ?? '') == 'average' ? 'selected' : '' }}>Average Stake</option>
                                <option value="highest_pool" {{ ($settings['default_result_logic'] ?? '') == 'highest_pool' ? 'selected' : '' }}>Highest Pool (House Wins Less)</option>
                            </select>
                        </div>
                    </div>

                    <hr>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-5">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 bg-light">
                <h5><i class="bi bi-info-circle"></i> Helper</h5>
                <p class="small text-muted">
                    <strong>Platform Fee:</strong> The percentage deducted from each bet before calculating winnings.
                    <br><br>
                    <strong>No-Bet Buffer:</strong> Users won't be able to place bets when the round is about to end within
                    this time frame.
                    <br><br>
                    <strong>Winning Logic:</strong> Used if no manual result is set by the admin.
                </p>
            </div>
        </div>
    </div>
@endsection