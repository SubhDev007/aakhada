@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Bet History</h1>
    </div>

    <div class="card">
        <div class="table-responsive p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Round</th>
                        <th>Number</th>
                        <th>Gross</th>
                        <th>Net</th>
                        <th>Status</th>
                        <th>Winnings</th>
                        <th>Placed At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bets as $bet)
                        <tr>
                            <td>#{{ $bet->id }}</td>
                            <td>
                                <div class="fw-bold">{{ $bet->user->name }}</div>
                                <small class="text-muted">{{ $bet->user->email }}</small>
                            </td>
                            <td>{{ $bet->round->round_serial ?? 'N/A' }}</td>
                            <td><span class="badge bg-secondary">#{{ $bet->chosen_number }}</span></td>
                            <td>₹{{ number_format($bet->gross_amount) }}</td>
                            <td>₹{{ number_format($bet->net_amount) }}</td>
                            <td>
                                @if($bet->status == 'won')
                                    <span class="badge bg-success">WIN</span>
                                @elseif($bet->status == 'lost')
                                    <span class="badge bg-danger">LOSS</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ strtoupper($bet->status) }}</span>
                                @endif
                            </td>
                            <td class="fw-bold {{ $bet->status == 'won' ? 'text-success' : '' }}">
                                {{ $bet->winnings > 0 ? '₹' . number_format($bet->winnings) : '-' }}
                            </td>
                            <td>{{ $bet->created_at->format('d M, H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white pt-3">
            {{ $bets->links() }}
        </div>
    </div>
@endsection