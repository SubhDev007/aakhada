@extends('layouts.bookman')

@section('title', 'My Users')

@section('content')
<div class="row w-100 m-0">
    <div class="col-12 p-0">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-people-fill me-2"></i>My Users
                </h5>
                <a href="{{ route('bookman.users.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Add User
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-secondary fw-semibold py-3 ps-4">ID</th>
                                <th class="text-secondary fw-semibold py-3">User</th>
                                <th class="text-secondary fw-semibold py-3">Wallet Balance</th>
                                <th class="text-secondary fw-semibold py-3">Joined</th>
                                <th class="text-secondary fw-semibold py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="ps-4 text-muted">#{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <span class="fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark d-block">{{ $user->name }}</span>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill border border-success border-opacity-25">
                                            ₹{{ number_format($user->wallet_balance, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $user->created_at->format('M d, Y') }}</td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal"
                                            data-bs-target="#addFundsModal{{ $user->id }}">
                                            <i class="bi bi-plus-circle me-1"></i> Add Funds
                                        </button>
                                    </td>
                                </tr>

                                <!-- Add Funds Modal -->
                                <div class="modal fade" id="addFundsModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4">
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold">Transfer Funds to {{ $user->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('bookman.users.add-funds', $user->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body py-4">
                                                    <div class="alert alert-info rounded-3 mb-4 border-0 bg-info bg-opacity-10 text-info-emphasis">
                                                        <i class="bi bi-info-circle-fill me-2"></i>
                                                        Your current balance: <strong>₹{{ number_format(auth()->user()->wallet_balance, 2) }}</strong>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-dark">Amount to Transfer (₹)</label>
                                                        <div class="input-group input-group-lg shadow-sm rounded-3">
                                                            <span class="input-group-text bg-light border-end-0">₹</span>
                                                            <input type="number" name="amount" class="form-control border-start-0 ps-0" required min="1" step="0.01" max="{{ auth()->user()->wallet_balance }}">
                                                        </div>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label fw-semibold text-dark">Description (Optional)</label>
                                                        <input type="text" name="description" class="form-control form-control-lg bg-light border-0 shadow-sm" placeholder="e.g. Winner payout">
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">Confirm Transfer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="py-4">
                                            <div class="mb-3">
                                                <i class="bi bi-people d-inline-block text-muted opacity-50" style="font-size: 4rem;"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark">No users found</h5>
                                            <p class="text-muted mb-4">You haven't added any users to your network yet.</p>
                                            <a href="{{ route('bookman.users.create') }}" class="btn btn-primary rounded-pill px-4">
                                                <i class="bi bi-plus-lg me-1"></i> Add Your First User
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
