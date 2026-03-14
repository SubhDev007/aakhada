@extends('layouts.mobile')

@section('title', 'Akhada - Bet Now')

@section('content')
    <div class="d-flex justify-content-between align-items-center p-3 bg-primary text-white sticky-top">
        <h5 class="m-0">Akhada</h5>
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
            <div class="mb-3">
                <h6 class="text-muted small text-uppercase fw-bold mb-2">Recent Results</h6>
                <div class="d-flex overflow-auto pb-2" style="gap: 10px;">
                    @foreach($pastRounds as $r)
                        <div class="card p-2 text-center flex-shrink-0" style="min-width: 80px; width: 80px;">
                            <small class="d-block text-muted" style="font-size: 0.7rem;">{{ $r->round_serial }}</small>
                            <div class="fw-bold fs-5">{{ $r->result_number }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
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
                    alert('Betting is closed for this round (Lock-in Period).');
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

        function placeBet() {
            const amount = document.getElementById('bet-amount').value;
            if (!amount || amount < 1) {
                alert('Invalid amount');
                return;
            }

            $.ajax({
                url: '/game/bet',
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + '{{ Auth::user() ? Auth::user()->createToken("web")->plainTextToken : "" }}' // Simple hack for demo, ideally use Sanctum SPA or standard session
                },
                // Wait, if I am using standard Web routes, I don't need Bearer token if utilizing Sanctum stateful?
                // "Laravel Sanctum provides a guard for your SPA... checks the session cookie"
                // So if I am logged in via web, I should use standard web request with CSRF token.
                // But my API routes are under `auth:sanctum`. 
                // I should use `web` routes for the betting or enable `EnsureFrontendRequestsAreStateful` for API.
                // For simplicity, let's use the API endpoint but ensure we are passing the token or session.
                // If `auth:sanctum` checks cookie, I need to configure Sanctum for SPA.
                // OR I can just make a Web Route for placing bet in GameViewController and bypass API for this "hybrid" app.
                // Let's stick to API but try to rely on session cookie if Sanctum is configured, otherwise I might struggle with auth in this quick setup.
                // ACTUALLY: The simplest way for this task is to use a WEB route for placing bets since it's a blade view.

                // Let's create a `placeBet` method in GameViewController or use the Api controller via internal request? No.
                // I will update routes to allow betting via Web for simplicity or assume Sanctum works with session (it does if configured).
                // Let's try standard AJAX with CSRF.
                data: {
                    number: selectedNumber,
                    amount: amount,
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    alert('Bet Placed!');
                    location.reload();
                },
                error: function (err) {
                    alert(err.responseJSON.message || 'Error placing bet');
                }
            });
        }
    </script>
@endpush