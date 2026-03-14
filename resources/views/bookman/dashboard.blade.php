@extends('layouts.bookman')

@section('content')
<div class="row w-100 m-0">
    <div class="col-12 p-0">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
            <div>
                <h2 class="fw-bold mb-0">Dashboard</h2>
                <small class="text-muted">Welcome back, {{ auth()->user()->name }}</small>
            </div>
            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill fw-semibold">
                <i class="bi bi-person-badge-fill me-1"></i> Bookman
            </span>
        </div>

        {{-- Stat Cards --}}
        <div class="row g-4 mb-4">

            {{-- Wallet Balance --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100 overflow-hidden">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold small mb-1">Wallet Balance</p>
                            <h2 class="fw-bold mb-0 text-dark">
                                ₹{{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}
                            </h2>
                            <small class="text-muted">Available in your account</small>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-success bg-opacity-10"
                            style="width: 64px; height: 64px;">
                            <i class="bi bi-wallet2 fs-2 text-success"></i>
                        </div>
                    </div>
                    <div class="px-4 pb-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Users Created --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100 overflow-hidden">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted text-uppercase fw-semibold small mb-1">Users Created</p>
                            <h2 class="fw-bold mb-0 text-dark">
                                {{ number_format($stats['total_users_created'] ?? 0) }}
                            </h2>
                            <small class="text-muted">Players in your network</small>
                        </div>
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10"
                            style="width: 64px; height: 64px;">
                            <i class="bi bi-people-fill fs-2 text-primary"></i>
                        </div>
                    </div>
                    <div class="px-4 pb-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Quick Actions --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('bookman.users.create') }}"
                            class="btn btn-danger w-100 py-3 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-person-plus-fill fs-5"></i>
                            Create New User
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('bookman.users.index') }}"
                            class="btn btn-outline-danger w-100 py-3 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-people fs-5"></i>
                            View All My Users
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
