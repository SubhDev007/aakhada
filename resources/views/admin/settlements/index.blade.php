@extends('layouts.admin')

@section('content')
<div class="row w-100 m-0">
    <div class="col-12 p-0">

        {{-- Header --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Daily Settlement Report</h4>
                    <p class="text-muted mb-0">Summary of all Bookman networks for {{ $reportDate->format('d M Y, l') }}</p>
                </div>
                <form method="GET" action="{{ route('admin.settlements.index') }}" class="d-flex align-items-center gap-2">
                    <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm" style="width: 170px;">
                    <button type="submit" class="btn btn-primary btn-sm px-3"><i class="bi bi-search me-1"></i>View</button>
                    <a href="{{ route('admin.settlements.index') }}" class="btn btn-outline-secondary btn-sm">Today</a>
                </form>
            </div>
        </div>

        {{-- Grand Totals --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3">
                        <p class="text-muted small text-uppercase fw-semibold mb-1">Total Deposited by Users</p>
                        <h4 class="fw-bold text-dark mb-0">₹{{ number_format($grandTotalDeposits, 2) }}</h4>
                        <small class="text-muted">Bookmen must send this to Admin tonight</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3">
                        <p class="text-muted small text-uppercase fw-semibold mb-1">Total User Winnings</p>
                        <h4 class="fw-bold text-danger mb-0">₹{{ number_format($grandTotalWinnings, 2) }}</h4>
                        <small class="text-muted">Bookmen must pay users</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3">
                        <p class="text-muted small text-uppercase fw-semibold mb-1">Admin Sends Tomorrow</p>
                        <h4 class="fw-bold text-warning mb-0">₹{{ number_format($grandNetPayBack, 2) }}</h4>
                        <small class="text-muted">Winnings + Commission to all Bookmen</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3">
                        <p class="text-muted small text-uppercase fw-semibold mb-1">Platform Profit</p>
                        <h4 class="fw-bold text-success mb-0">₹{{ number_format($grandProfit, 2) }}</h4>
                        <small class="text-muted">After winnings + commissions</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Per Bookman Breakdown --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold">Per Bookman Breakdown</h6>
            </div>
            <div class="card-body p-0">
                @if(count($settlements) === 0)
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-person-badge fs-1 d-block mb-2 opacity-25"></i>
                        <p>No Bookmen found.</p>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 ps-4 text-secondary fw-semibold">Bookman</th>
                                <th class="py-3 text-secondary fw-semibold text-end">Bets</th>
                                <th class="py-3 text-secondary fw-semibold text-end">
                                    <span class="text-dark">① Total Collected</span>
                                    <div class="small fw-normal text-muted">Bookman sends to Admin</div>
                                </th>
                                <th class="py-3 text-secondary fw-semibold text-end">
                                    <span class="text-danger">② User Winnings</span>
                                    <div class="small fw-normal text-muted">Bookman pays users</div>
                                </th>
                                <th class="py-3 text-secondary fw-semibold text-end">
                                    <span class="text-info">③ Commission (5%)</span>
                                    <div class="small fw-normal text-muted">Bookman's earnings</div>
                                </th>
                                <th class="py-3 text-secondary fw-semibold text-end">
                                    <span class="text-warning">Admin Pays Tomorrow</span>
                                    <div class="small fw-normal text-muted">② + ③</div>
                                </th>
                                <th class="py-3 text-secondary fw-semibold text-end pe-4">
                                    <span class="text-success">Platform Profit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($settlements as $s)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold text-dark">{{ $s['bookman']->name }}</div>
                                    <small class="text-muted">{{ $s['bookman']->email }}</small>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $s['bet_count'] }}</span>
                                </td>
                                <td class="text-end fw-bold text-dark">₹{{ number_format($s['total_deposits'], 2) }}</td>
                                <td class="text-end fw-bold text-danger">₹{{ number_format($s['total_winnings'], 2) }}</td>
                                <td class="text-end fw-bold text-info">₹{{ number_format($s['bookman_commission'], 2) }}</td>
                                <td class="text-end">
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6">
                                        ₹{{ number_format($s['net_to_pay_back'], 2) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    @if($s['platform_profit'] >= 0)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                            +₹{{ number_format($s['platform_profit'], 2) }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">
                                            -₹{{ number_format(abs($s['platform_profit']), 2) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light border-top">
                            <tr>
                                <td class="ps-4 fw-bold text-dark py-3" colspan="2">TOTAL</td>
                                <td class="text-end fw-bold text-dark">₹{{ number_format($grandTotalDeposits, 2) }}</td>
                                <td class="text-end fw-bold text-danger">₹{{ number_format($grandTotalWinnings, 2) }}</td>
                                <td class="text-end fw-bold text-info">₹{{ number_format($grandTotalCommission, 2) }}</td>
                                <td class="text-end fw-bold text-warning">₹{{ number_format($grandNetPayBack, 2) }}</td>
                                <td class="text-end pe-4 fw-bold text-success">₹{{ number_format($grandProfit, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- Legend --}}
        <div class="alert alert-info border-0 rounded-3 bg-info bg-opacity-10 mt-4">
            <h6 class="fw-bold"><i class="bi bi-info-circle-fill me-2"></i>How to use this report</h6>
            <ol class="mb-0 small">
                <li><strong>Evening:</strong> Collect <span class="fw-bold text-dark">① Total Collected</span> cash from each Bookman.</li>
                <li><strong>Next Morning:</strong> Send <span class="fw-bold text-warning">Admin Pays Tomorrow</span> cash back to each Bookman (User Winnings + their Commission).</li>
                <li><strong>Your profit</strong> is the <span class="fw-bold text-success">Platform Profit</span> column — what you keep after all payouts.</li>
            </ol>
        </div>

    </div>
</div>
@endsection
