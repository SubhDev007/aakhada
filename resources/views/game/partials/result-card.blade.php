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
