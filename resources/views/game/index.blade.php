@extends('layouts.mobile')

@section('title', 'Aakhada - Bet Now')

@section('content')
    <div class="d-flex justify-content-between align-items-center p-3 bg-primary text-white sticky-top">
        <h5 class="m-0">Aakhada</h5>
        <div class="d-flex align-items-center">
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-light me-2">Admin</a>
            @endif
            <span class="me-2">₹ <span id="wallet-balance">{{ Auth::user()->wallet_balance ?? 0 }}</span></span>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
            </form>
        </div>
    </div>

    <div class="container mt-3">
        <!-- Round Info -->
        <div class="stats-card p-3 mb-3 text-center">
            @if($round)
                <h6 class="text-uppercase opacity-75">Current Round</h6>
                <h3 id="round-serial">{{ $round->name ?? $round->round_serial }}</h3>
                <div class="display-6 fw-bold" id="countdown">00:00:00</div>
                <small>Ends at: {{ $round->end_time->format('h:i A') }}</small>
            @elseif($nextRound)
                <h6 class="text-uppercase opacity-75 text-warning">Next Round</h6>
                <h3 id="round-serial">{{ $nextRound->name ?? $nextRound->round_serial }}</h3>
                <div class="display-6 fw-bold text-warning" id="countdown">00:00:00</div>
                <small>Starts at: {{ $nextRound->start_time->format('h:i A') }}</small>
                <div class="mt-2"><span class="badge bg-warning text-dark">Betting opens soon</span></div>
            @else
                <h6 class="text-uppercase opacity-75 text-muted">No Round Active</h6>
                <div class="display-6 fw-bold text-muted" id="countdown">--:--:--</div>
                <small class="text-muted">Check back later for the next round.</small>
            @endif
        </div>


        <!-- Betting Grid -->
        <div class="game-grid mb-4">
            @foreach(range(0, 9) as $num)
                <div class="number-btn btn btn-outline-primary position-relative" onclick="openBetModal({{ $num }})">
                    {{ $num }}
                </div>
            @endforeach
        </div>

        @if($pastRounds->count() > 0)
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted small text-uppercase fw-bold m-0">Recent Results</h6>
                    <small class="text-primary fw-bold" style="font-size: 0.75rem;">Scroll →</small>
                </div>
                <div class="results-container d-flex overflow-auto pb-2" style="gap: 12px; scrollbar-width: none; -ms-overflow-style: none;">
                    @foreach($pastRounds as $r)
                        @php
                            $colorMap = [
                                0 => '#e91e63', // Deep Pink
                                1 => '#9c27b0', // Purple
                                2 => '#673ab7', // Deep Purple
                                3 => '#3f51b5', // Indigo
                                4 => '#2196f3', // Blue
                                5 => '#009688', // Teal
                                6 => '#4caf50', // Green
                                7 => '#ffc107', // Amber
                                8 => '#ff9800', // Orange
                                9 => '#f44336'  // Red
                            ];
                            $color = $colorMap[$r->result_number % 10] ?? '#6c757d';
                        @endphp
                        <div class="result-card bg-white shadow-sm border-0 rounded-4 p-2 text-center flex-shrink-0" 
                             style="min-width: 80px; width: 80px; transition: transform 0.2s;">
                            <small class="d-block text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.6rem; letter-spacing: 0.5px;">{{ $r->name ?? 'Round' }}</small>
                            <div class="result-number-circle mx-auto d-flex align-items-center justify-content-center fw-bold fs-4 text-white" 
                                 style="width: 45px; height: 45px; border-radius: 50%; background: {{ $color }}; box-shadow: 0 4px 10px {{ $color }}40;">
                                {{ $r->result_number }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <style>
                .results-container::-webkit-scrollbar {
                    display: none;
                }
                .result-card:active {
                    transform: scale(0.95);
                }
                .result-number-circle {
                    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
            </style>
        @endif



        <!-- Active Bets -->
        <h6 class="text-muted border-bottom pb-2">My Active Bets</h6>
        <div id="active-bets-list" class="list-group list-group-flush mb-4 small">
            @forelse($activeBets as $bet)
                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                    <div>
                        <span class="badge bg-secondary rounded-pill me-2">#{{ $bet->chosen_number }}</span>
                        <span class="text-muted">{{ $bet->created_at->format('H:i') }}</span>
                    </div>
                    <div class="fw-bold">₹{{ number_format($bet->gross_amount) }}</div>
                </div>
            @empty
                <div class="text-muted text-center py-2">No active bets for this round.</div>
            @endforelse
        </div>

        <!-- Bet History -->
        @if($betHistory->count() > 0)
            <h6 class="text-muted border-bottom pb-2 mt-4">My Bet History</h6>
            <div class="list-group list-group-flush mb-4 small">
                @foreach($betHistory as $bet)
                    <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                        <div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary rounded-pill me-2">#{{ $bet->chosen_number }}</span>
                                @if($bet->status == 'won')
                                    <span class="badge bg-success">WIN</span>
                                @elseif($bet->status == 'lost')
                                    <span class="badge bg-danger">LOSS</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ strtoupper($bet->status) }}</span>
                                @endif
                            </div>
                            <small class="text-muted d-block mt-1">{{ $bet->round->round_serial ?? 'N/A' }} |
                                {{ $bet->created_at->format('d M H:i') }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold fs-6">
                                @if($bet->status == 'won')
                                    <span class="text-success">+₹{{ number_format($bet->winnings) }}</span>
                                @elseif($bet->status == 'lost')
                                    <span class="text-danger">-₹{{ number_format($bet->gross_amount) }}</span>
                                @else
                                    <span>₹{{ number_format($bet->gross_amount) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Bet Modal -->
    <div class="modal fade" id="betModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bet on Number <span id="selected-number"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="number" id="bet-amount" class="form-control form-control-lg mb-3" placeholder="Amount (₹)"
                        min="10">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-secondary" onclick="setAmount(50)">+50</button>
                        <button class="btn btn-outline-secondary" onclick="setAmount(100)">+100</button>
                        <button class="btn btn-outline-secondary" onclick="setAmount(500)">+500</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary w-100" onclick="placeBet()">Place Bet</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let selectedNumber = null;
        let roundEndTime   = "{{ $round ? $round->end_time->toIso8601String() : '' }}";
        let roundStartTime = "{{ $nextRound ? $nextRound->start_time->toIso8601String() : '' }}";
        let isUpcoming     = {{ $round ? 'false' : ($nextRound ? 'true' : 'false') }};
        let noBetBufferSeconds = {{ $noBetBufferSeconds ?? 0 }};
        let reloadScheduled = false;

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        function updateTimer() {
            const now = new Date();
            const grid = document.querySelector('.game-grid');

            if (isUpcoming && roundStartTime) {
                // Countdown to when betting OPENS
                const start = new Date(roundStartTime);
                const diff = start - now;

                if (diff <= 0) {
                    // Round has started — reload to activate it
                    if (!reloadScheduled) {
                        reloadScheduled = true;
                        setTimeout(() => location.reload(), 1000);
                    }
                    document.getElementById('countdown').innerText = "Starting...";
                    return;
                }

                // Lock the grid while waiting
                if (grid) grid.classList.add('opacity-50', 'pe-none');

                const h = Math.floor(diff / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((diff % (1000 * 60)) / 1000);
                document.getElementById('countdown').innerText =
                    `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                return;
            }

            if (!roundEndTime) {
                document.getElementById('countdown').innerText = "--:--:--";
                return;
            }

            const end = new Date(roundEndTime);
            const diff = end - now;

            // No-bet buffer lock
            if (typeof noBetBufferSeconds !== 'undefined') {
                const diffSeconds = diff / 1000;
                if (diffSeconds < noBetBufferSeconds) {
                    if (grid && !grid.classList.contains('opacity-50')) {
                        grid.classList.add('opacity-50', 'pe-none');
                    }
                } else {
                    if (grid && grid.classList.contains('opacity-50')) {
                        grid.classList.remove('opacity-50', 'pe-none');
                    }
                }
            }

            if (diff <= 0) {
                document.getElementById('countdown').innerText = "00:00:00";
                if (!reloadScheduled) {
                    reloadScheduled = true;
                    setTimeout(() => location.reload(), 3000);
                }
                return;
            }

            const h = Math.floor(diff / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);
            document.getElementById('countdown').innerText =
                `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        }

        setInterval(updateTimer, 1000);
        updateTimer();



        function openBetModal(num) {
            // Check Lock-in
            if (roundEndTime) {
                const now = new Date();
                const end = new Date(roundEndTime);
                const diffSeconds = (end - now) / 1000;

                if (diffSeconds < noBetBufferSeconds) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Betting is closed for this round.'
                    });
                    return;
                }
            }

            selectedNumber = num;
            document.getElementById('selected-number').innerText = num;
            document.getElementById('bet-amount').value = '';
            new bootstrap.Modal(document.getElementById('betModal')).show();
        }

        function setAmount(amt) {
            const input = document.getElementById('bet-amount');
            input.value = (parseInt(input.value || 0) + amt);
        }

        function placeBet(customAmount = null) {
            const amount = customAmount || document.getElementById('bet-amount').value;
            if (!amount || amount < 1) {
                Toast.fire({
                    icon: 'error',
                    title: 'Invalid amount'
                });
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Placing Bet...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '/game/bet',
                method: 'POST',
                data: {
                    number: selectedNumber,
                    amount: amount,
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Bet Placed Successfully!'
                    });
                    setTimeout(() => location.reload(), 1500);
                },
                error: function (err) {
                    Swal.close(); // Close loading
                    Toast.fire({
                        icon: 'error',
                        title: err.responseJSON.message || 'Error placing bet'
                    });
                }
            });
        }
    </script>
@endpush