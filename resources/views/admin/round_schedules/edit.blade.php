@extends('layouts.admin')

@section('content')
<div class="row justify-content-center m-0">
    <div class="col-md-7 p-0 mt-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-pencil-fill me-2"></i>Edit Round: {{ $roundSchedule->name }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.round-schedules.update', $roundSchedule) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Round Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $roundSchedule->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                                value="{{ old('start_time', \Carbon\Carbon::createFromFormat('H:i:s', $roundSchedule->start_time)->format('H:i')) }}" required>
                            @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Duration (minutes) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror"
                                value="{{ old('duration_minutes', $roundSchedule->duration_minutes) }}" min="1" max="1440" required>
                            @error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small id="endTimePreview" class="text-muted"></small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control"
                            value="{{ old('sort_order', $roundSchedule->sort_order) }}" min="0">
                    </div>

                    <div class="alert alert-warning border-0 rounded-3 bg-warning bg-opacity-10 text-warning-emphasis">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Changes to time/duration apply from <strong>tomorrow</strong> — today's generated rounds are unaffected.
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('admin.round-schedules.index') }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Update Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateEndPreview() {
        const startInput = document.querySelector('input[name="start_time"]').value;
        const duration = parseInt(document.querySelector('input[name="duration_minutes"]').value) || 0;
        if (startInput && duration > 0) {
            const [h, m] = startInput.split(':').map(Number);
            const total = h * 60 + m + duration;
            const endH = String(Math.floor(total / 60) % 24).padStart(2, '0');
            const endM = String(total % 60).padStart(2, '0');
            const ampm = endH >= 12 ? 'PM' : 'AM';
            const displayH = endH % 12 || 12;
            document.getElementById('endTimePreview').textContent = `Round ends at ${displayH}:${endM} ${ampm}`;
        }
    }
    document.querySelector('input[name="start_time"]').addEventListener('change', updateEndPreview);
    document.querySelector('input[name="duration_minutes"]').addEventListener('input', updateEndPreview);
    updateEndPreview();
</script>
@endpush
@endsection
