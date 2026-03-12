@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Live Monitoring - <span
                id="round-serial">{{ $round ? $round->round_serial : 'No Active Round' }}</span></h1>
        <div class="text-muted">Auto-refreshing every 5 seconds</div>
    </div>

    <div class="row" id="monitoring-grid">
        <input type="hidden" id="current-round-id" value="{{ $round ? $round->id : '' }}">
        @foreach($formattedStats as $num => $stat)
            <div class="col-md-3 col-lg-2 mb-4">
                <div class="card text-center p-3 border-primary h-100" style="background: #fff;">
                    <div class="display-6 fw-bold text-primary mb-2">{{ $num }}</div>
                    <div class="small text-muted text-uppercase fw-bold">Stake</div>
                    <div class="h4 mb-2">₹<span class="stake-val">{{ number_format($stat['total_stake']) }}</span></div>
                    <div class="small text-muted text-uppercase fw-bold">Bets</div>
                    <div class="h5 mb-3"><span class="count-val">{{ number_format($stat['bet_count']) }}</span></div>

                    <button class="btn btn-sm btn-outline-success w-100 set-winner-btn" 
                            onclick="setWinner({{ $num }})" 
                            {{ !$round ? 'disabled' : '' }}>
                        Set Winner
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mt-4 p-3 bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong>Status:</strong> <span id="round-status"
                    class="badge bg-success">{{ $round ? $round->status : 'N/A' }}</span>
            </div>
            <div>
                <strong>Ends in:</strong> <span id="round-timer" class="fw-bold">--:--:--</span>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let roundEndTime = "{{ $round ? $round->end_time->toIso8601String() : '' }}";

        function updateTimer() {
            if (!roundEndTime) return;
            const now = new Date();
            const end = new Date(roundEndTime);
            const diff = end - now;

            if (diff <= 0) {
                document.getElementById('round-timer').innerText = "00:00:00";
                return;
            }

            const h = Math.floor(diff / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById('round-timer').innerText =
                `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        }

        function refreshData() {
            $.get("{{ route('admin.monitoring') }}", function (res) {
                if (res.round) {
                    $('#round-serial').text(res.round.round_serial);
                    $('#round-status').text(res.round.status);
                    $('#current-round-id').val(res.round.id);
                    $('.set-winner-btn').prop('disabled', false);
                    roundEndTime = res.round.end_time;
                } else {
                    $('#round-serial').text('No Active Round');
                    $('#current-round-id').val('');
                    $('.set-winner-btn').prop('disabled', true);
                    roundEndTime = '';
                }

                Object.keys(res.stats).forEach(num => {
                    const card = $(`#monitoring-grid .col-md-3, #monitoring-grid .col-md-2`).eq(num);
                    card.find('.stake-val').text(res.stats[num].total_stake.toLocaleString());
                    card.find('.count-val').text(res.stats[num].bet_count.toLocaleString());
                });
            });
        }

        function setWinner(num) {
            const roundId = $('#current-round-id').val();
            if(!roundId) {
                alert('No active round to set winner for.');
                return;
            }

            if(!confirm(`Are you sure you want to set Number ${num} as the winner for this round? This will override the automatic logic.`)) {
                return;
            }

            $.post("{{ route('admin.set-result') }}", {
                round_id: roundId,
                winning_number: num,
                _token: "{{ csrf_token() }}"
            })
            .done(function(res) {
                alert(res.message);
            })
            .fail(function(err) {
                alert(err.responseJSON?.message || 'Error setting winner');
            });
        }

        setInterval(updateTimer, 1000);
        setInterval(refreshData, 5000);
        updateTimer();
    </script>
@endpush