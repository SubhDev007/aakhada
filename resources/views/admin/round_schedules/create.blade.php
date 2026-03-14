@extends('layouts.admin')

@section('content')
<div class="row justify-content-center m-0">
    <div class="col-md-7 p-0 mt-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="bi bi-plus-circle-fill me-2"></i>Add Round Schedule
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.round-schedules.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Round Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="e.g. Morning Gold, Evening Premier" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">This name appears in the user game screen and bet history.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Start Time <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                                value="{{ old('start_time') }}" required>
                            @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Duration (minutes) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror"
                                value="{{ old('duration_minutes', 15) }}" min="1" max="1440" required>
                            @error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small id="endTimePreview" class="text-muted"></small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control"
                            value="{{ old('sort_order', 0) }}" min="0">
                        <small class="text-muted">Lower numbers appear first. Used for display ordering only.</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('admin.round-schedules.index') }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Save Round Schedule</button>
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
        } else {
            document.getElementById('endTimePreview').textContent = '';
        }
    }
    document.querySelector('input[name="start_time"]').addEventListener('change', updateEndPreview);
    document.querySelector('input[name="duration_minutes"]').addEventListener('input', updateEndPreview);
</script>
@endpush
@endsection
