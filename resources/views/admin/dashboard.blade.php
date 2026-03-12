@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white p-3">
                <h6 class="text-uppercase opacity-75">Total Bets</h6>
                <h2 class="mb-0">{{ number_format($stats['total_bets']) }}</h2>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white p-3">
                <h6 class="text-uppercase opacity-75">Total Stake</h6>
                <h2 class="mb-0">₹{{ number_format($stats['total_stake']) }}</h2>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white p-3">
                <h6 class="text-uppercase opacity-75">Pending Bets</h6>
                <h2 class="mb-0">{{ number_format($stats['pending_bets']) }}</h2>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark p-3">
                <h6 class="text-uppercase opacity-75">Current Round</h6>
                <h2 class="mb-0">{{ $stats['active_round'] ? $stats['active_round']->round_serial : 'None' }}</h2>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card p-4 text-center">
                <h3>Quick Actions</h3>
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <a href="{{ route('admin.monitoring') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-eye"></i> Launch Monitor
                    </a>
                    <form action="{{ route('admin.process') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="bi bi-arrow-repeat"></i> Process Rounds Manually
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection