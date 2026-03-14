@extends('layouts.admin')

@section('content')
<div class="row w-100 m-0">
    <div class="col-12 p-0">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-people-fill me-2"></i>Bookmen Management
                </h5>
                <a href="{{ route('admin.bookmen.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Add Bookman
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-secondary fw-semibold py-3 ps-4">ID</th>
                                <th class="text-secondary fw-semibold py-3">Name</th>
                                <th class="text-secondary fw-semibold py-3">Email</th>
                                <th class="text-secondary fw-semibold py-3">Wallet Balance</th>
                                <th class="text-secondary fw-semibold py-3">Joined</th>
                                <th class="text-secondary fw-semibold py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookmen as $bookman)
                                <tr>
                                    <td class="ps-4 text-muted">#{{ $bookman->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ strtoupper(substr($bookman->name, 0, 1)) }}
                                            </div>
                                            <span class="fw-medium text-dark">{{ $bookman->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $bookman->email }}</td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill">
                                            ₹{{ number_format($bookman->wallet_balance, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $bookman->created_at->format('M d, Y') }}</td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                            data-bs-target="#addFundsModal{{ $bookman->id }}">
                                            <i class="bi bi-currency-rupee"></i> Add Funds
                                        </button>
                                    </td>
                                </tr>

                                <!-- Add Funds Modal -->
                                <div class="modal fade" id="addFundsModal{{ $bookman->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Add Funds to {{ $bookman->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.bookmen.add-funds', $bookman->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Amount (₹)</label>
                                                        <input type="number" name="amount" class="form-control" required min="1" step="0.01">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description (Optional)</label>
                                                        <input type="text" name="description" class="form-control" placeholder="Optional remark">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Add Funds</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        No bookmen found in the system.
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
