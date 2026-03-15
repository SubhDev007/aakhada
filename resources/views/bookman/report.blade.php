@extends('layouts.bookman')

@section('content')
<div class="row w-100 m-0">
    <div class="col-12 p-0">

        {{-- Header --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-receipt-cutoff me-2 text-danger"></i>My Daily Report</h4>
                    <p class="text-muted mb-0">Settlement summary for {{ $reportDate->format('d M Y, l') }}</p>
                </div>
                <form method="GET" action="{{ route('bookman.report') }}" class="d-flex align-items-center gap-2">
                    <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm" style="width: 170px;">
                    <button type="submit" class="btn btn-danger btn-sm px-3"><i class="bi bi-search me-1"></i>View</button>
                    <a href="{{ route('bookman.report') }}" class="btn btn-outline-secondary btn-sm">Today</a>
                </form>
            </div>
        </div>

        {{-- Settlement Summary Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <p class="text-muted text-uppercase fw-semibold small mb-1">🌇 Send to Admin Tonight</p>
                        <h3 class="fw-bold text-dark mb-1">₹{{ number_format($toSendAdmin, 2) }}</h3>
                        <small class="text-muted">Total cash collected from your users today</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <p class="text-muted text-uppercase fw-semibold small mb-1">🌅 Receive from Admin Tomorrow</p>
                        <h3 class="fw-bold text-warning mb-1">₹{{ number_format($toReceiveMorning, 2) }}</h3>
                        <small class="text-muted">User winnings (₹{{ number_format($totalWinnings,2) }}) + Your commission (₹{{ number_format($myCommission,2) }})</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <p class="text-muted text-uppercase fw-semibold small mb-1">💰 Your Commission ({{ $commissionRate }}%)</p>
                        <h3 class="fw-bold text-success mb-1">₹{{ number_format($myCommission, 2) }}</h3>
                        <small class="text-muted">{{ $commissionRate }}% of ₹{{ number_format($totalCollected, 2) }} total collected</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Per User Breakdown --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Your Users Breakdown</h6>
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">
                    {{ count($userReports) }} Users
                </span>
            </div>
            <div class="card-body p-0">
                @if(count($userReports) === 0)
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                        <p>You have no users yet.</p>
                        <a href="{{ route('bookman.users.create') }}" class="btn btn-danger btn-sm">Create Your First User</a>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 ps-4 text-secondary fw-semibold">User</th>
                                <th class="py-3 text-secondary fw-semibold text-center">Bets Placed</th>
                                <th class="py-3 text-secondary fw-semibold text-end">Total Deposited</th>
                                <th class="py-3 text-secondary fw-semibold text-end">Won</th>
                                <th class="py-3 text-secondary fw-semibold text-end">Lost</th>
                                <th class="py-3 text-secondary fw-semibold text-end pe-4">
                                    Win/Loss for User
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userReports as $r)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold text-dark">{{ $r['user']->name }}</div>
                                    <small class="text-muted">{{ $r['user']->email }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $r['bet_count'] }}</span>
                                </td>
                                <td class="text-end fw-semibold text-dark">₹{{ number_format($r['total_deposited'], 2) }}</td>
                                <td class="text-end">
                                    @if($r['total_won'] > 0)
                                        <span class="text-danger fw-semibold">-₹{{ number_format($r['total_won'], 2) }}</span>
                                    @else
                                        <span class="text-muted">₹0</span>
                                    @endif
                                </td>
                                <td class="text-end text-muted">₹{{ number_format($r['total_lost'], 2) }}</td>
                                <td class="text-end pe-4">
                                    @if($r['net'] >= 0)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                            +₹{{ number_format($r['net'], 2) }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">
                                            -₹{{ number_format(abs($r['net']), 2) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light border-top">
                            <tr>
                                <td class="ps-4 fw-bold py-3" colspan="2">TOTAL</td>
                                <td class="text-end fw-bold text-dark">₹{{ number_format($totalCollected, 2) }}</td>
                                <td class="text-end fw-bold text-danger">-₹{{ number_format($totalWinnings, 2) }}</td>
                                <td class="text-end fw-bold text-muted"></td>
                                <td class="text-end pe-4 fw-bold text-success">₹{{ number_format($totalCollected - $totalWinnings, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- Guide --}}
        <div class="alert alert-danger border-0 rounded-3 bg-danger bg-opacity-10 mt-4">
            <h6 class="fw-bold"><i class="bi bi-info-circle-fill me-2"></i>How Settlement Works</h6>
            <ol class="mb-0 small">
                <li><strong>Tonight (🌇):</strong> Send <strong>₹{{ number_format($toSendAdmin, 2) }}</strong> (Total Collected) to Admin.</li>
                <li><strong>Tomorrow Morning (🌅):</strong> Admin sends you <strong>₹{{ number_format($toReceiveMorning, 2) }}</strong>. This includes all User Winnings + your {{ $commissionRate }}% commission.</li>
                <li><strong>Payout:</strong> Use the money received from Admin to pay your winning users. Your fixed profit is the commission, regardless of whether users win or lose.</li>
            </ol>
        </div>

    </div>
</div>
@endsection
