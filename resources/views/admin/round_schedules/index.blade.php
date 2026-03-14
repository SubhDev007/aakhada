@extends('layouts.admin')

@section('content')
<div class="row w-100 m-0">
    <div class="col-12 p-0">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-clock-history me-2"></i>Round Schedules
                </h5>
                <a href="{{ route('admin.round-schedules.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Add Round
                </a>
            </div>
            <div class="card-body p-0">
                @if($schedules->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-clock fs-1 d-block mb-2 opacity-25"></i>
                        <p class="mb-3">No round schedules configured yet.</p>
                        <a href="{{ route('admin.round-schedules.create') }}" class="btn btn-primary">Add Your First Round</a>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 ps-4 text-secondary fw-semibold">Round Name</th>
                                <th class="py-3 text-secondary fw-semibold">Start Time</th>
                                <th class="py-3 text-secondary fw-semibold">End Time</th>
                                <th class="py-3 text-secondary fw-semibold">Duration</th>
                                <th class="py-3 text-secondary fw-semibold">Status</th>
                                <th class="py-3 text-secondary fw-semibold text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <i class="bi bi-circle-fill me-2 {{ $schedule->is_active ? 'text-success' : 'text-secondary' }}" style="font-size: 0.5rem; vertical-align: middle;"></i>
                                    {{ $schedule->name }}
                                </td>
                                <td class="text-dark">{{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time)->format('h:i A') }}</td>
                                <td class="text-dark">{{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->end_time)->format('h:i A') }}</td>
                                <td class="text-muted">{{ $schedule->duration_minutes }} min</td>
                                <td>
                                    @if($schedule->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Active</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2 rounded-pill">Disabled</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.round-schedules.edit', $schedule) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.round-schedules.toggle', $schedule) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm {{ $schedule->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $schedule->is_active ? 'Disable' : 'Enable' }}">
                                                <i class="bi bi-{{ $schedule->is_active ? 'pause-fill' : 'play-fill' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.round-schedules.destroy', $schedule) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this round schedule?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        <div class="alert alert-info mt-3 border-0 rounded-3 bg-info bg-opacity-10 text-info-emphasis">
            <i class="bi bi-info-circle-fill me-2"></i>
            <strong>Note:</strong> Rounds run on their configured schedule daily, automatically. Changes take effect from the next day's generation. Disable a round to stop it from being generated tomorrow.
        </div>
    </div>
</div>
@endsection
