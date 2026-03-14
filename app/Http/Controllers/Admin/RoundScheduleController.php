<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoundSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RoundScheduleController extends Controller
{
    public function index()
    {
        $schedules = RoundSchedule::orderBy('start_time')->get();
        return view('admin.round_schedules.index', compact('schedules'));
    }

    public function create()
    {
        return view('admin.round_schedules.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100|unique:round_schedules,name',
            'start_time'       => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:1|max:1440',
            'sort_order'       => 'nullable|integer|min:0',
        ]);

        // Normalize to H:i:s
        $startTime = Carbon::createFromFormat('H:i', $data['start_time'])->format('H:i:s');
        $endTime   = Carbon::createFromFormat('H:i:s', $startTime)->addMinutes((int) $data['duration_minutes'])->format('H:i:s');

        // No-overlap check
        $overlap = $this->checkOverlap($startTime, $endTime);
        if ($overlap) {
            return back()->withInput()->withErrors([
                'start_time' => "This time overlaps with existing schedule \"{$overlap->name}\" ({$overlap->start_time} – {$overlap->end_time}). Please choose a different time.",
            ]);
        }

        RoundSchedule::create([
            'name'             => $data['name'],
            'start_time'       => $startTime,
            'duration_minutes' => $data['duration_minutes'],
            'sort_order'       => $data['sort_order'] ?? 0,
            'is_active'        => true,
        ]);

        return redirect()->route('admin.round-schedules.index')->with('success', 'Round schedule created successfully.');
    }

    public function edit(RoundSchedule $roundSchedule)
    {
        return view('admin.round_schedules.edit', compact('roundSchedule'));
    }

    public function update(Request $request, RoundSchedule $roundSchedule)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100|unique:round_schedules,name,' . $roundSchedule->id,
            'start_time'       => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:1|max:1440',
            'sort_order'       => 'nullable|integer|min:0',
        ]);

        $startTime = Carbon::createFromFormat('H:i', $data['start_time'])->format('H:i:s');
        $endTime   = Carbon::createFromFormat('H:i:s', $startTime)->addMinutes((int) $data['duration_minutes'])->format('H:i:s');

        // No-overlap check (exclude self)
        $overlap = $this->checkOverlap($startTime, $endTime, excludeId: $roundSchedule->id);
        if ($overlap) {
            return back()->withInput()->withErrors([
                'start_time' => "This time overlaps with existing schedule \"{$overlap->name}\" ({$overlap->start_time} – {$overlap->end_time}). Please choose a different time.",
            ]);
        }

        $roundSchedule->update([
            'name'             => $data['name'],
            'start_time'       => $startTime,
            'duration_minutes' => $data['duration_minutes'],
            'sort_order'       => $data['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.round-schedules.index')->with('success', 'Round schedule updated successfully.');
    }

    public function toggleActive(RoundSchedule $roundSchedule)
    {
        $roundSchedule->update(['is_active' => !$roundSchedule->is_active]);
        $status = $roundSchedule->is_active ? 'enabled' : 'disabled';
        return redirect()->route('admin.round-schedules.index')->with('success', "Round \"{$roundSchedule->name}\" {$status}.");
    }

    public function destroy(RoundSchedule $roundSchedule)
    {
        $roundSchedule->delete();
        return redirect()->route('admin.round-schedules.index')->with('success', 'Round schedule deleted.');
    }

    /**
     * Check for overlapping active schedules.
     * Returns the conflicting schedule or null.
     */
    private function checkOverlap(string $startTime, string $endTime, ?int $excludeId = null): ?RoundSchedule
    {
        $query = RoundSchedule::where('is_active', true);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        foreach ($query->get() as $schedule) {
            if ($schedule->overlapsWith($startTime, $endTime)) {
                return $schedule;
            }
        }
        return null;
    }
}
