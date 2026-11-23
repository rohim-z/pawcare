<?php

namespace App\Http\Controllers;

use App\Models\CareSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CareScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $pets = $user->pets()->orderBy('name')->get();
        $petIds = $pets->pluck('id');

        $query = CareSchedule::with('pet')
            ->whereIn('pet_id', $petIds);

        // Filter berdasarkan hewan
        if ($request->filled('pet_id')) {
            $query->where('pet_id', $request->pet_id);
        }

        // Filter berdasarkan tab status
        $filterStatus = $request->get('status', 'all');
        $today = now()->toDateString();

        if ($filterStatus === 'upcoming') {
            $query->where('status', 'pending')
                  ->whereDate('scheduled_date', '>=', $today);
        } elseif ($filterStatus === 'overdue') {
            $query->where('status', 'pending')
                  ->whereDate('scheduled_date', '<', $today);
        } elseif ($filterStatus === 'completed') {
            $query->where('status', 'completed');
        }

        // Urutkan yang pending di atas, lalu berdasarkan tanggal & jam terdekat
        $schedules = $query->orderByRaw("FIELD(status, 'pending', 'completed')")
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('scheduled_time', 'asc')
            ->get();

        return view('care_schedules.index', compact('schedules', 'pets', 'filterStatus'));
    }

    public function create()
    {
        $pets = Auth::user()->pets()->orderBy('name')->get();

        if ($pets->isEmpty()) {
            return redirect()->route('pets.create')
                ->with('error', 'Tambahkan hewan peliharaan terlebih dahulu sebelum membuat jadwal.');
        }

        return view('care_schedules.create', compact('pets'));
    }

    public function store(Request $request)
    {
        $petIds = Auth::user()->pets()->pluck('id')->toArray();

        $validated = $request->validate([
            'pet_id' => 'required|in:' . implode(',', $petIds),
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'pending';

        CareSchedule::create($validated);

        return redirect()->route('care-schedules.index')
            ->with('success', 'Jadwal perawatan berhasil ditambahkan.');
    }

    public function edit(CareSchedule $careSchedule)
    {
        $this->authorizeSchedule($careSchedule);
        $pets = Auth::user()->pets()->orderBy('name')->get();

        return view('care_schedules.edit', compact('careSchedule', 'pets'));
    }

    public function update(Request $request, CareSchedule $careSchedule)
    {
        $this->authorizeSchedule($careSchedule);
        $petIds = Auth::user()->pets()->pluck('id')->toArray();

        $validated = $request->validate([
            'pet_id' => 'required|in:' . implode(',', $petIds),
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable',
            'status' => 'required|in:pending,completed',
            'notes' => 'nullable|string',
        ]);

        $careSchedule->update($validated);

        return redirect()->route('care-schedules.index')
            ->with('success', 'Jadwal perawatan berhasil diperbarui.');
    }

    public function toggleComplete(CareSchedule $careSchedule)
    {
        $this->authorizeSchedule($careSchedule);

        $careSchedule->update([
            'status' => $careSchedule->status === 'completed' ? 'pending' : 'completed',
        ]);

        return back()->with('success', 'Status jadwal berhasil diperbarui.');
    }

    public function destroy(CareSchedule $careSchedule)
    {
        $this->authorizeSchedule($careSchedule);
        $careSchedule->delete();

        return redirect()->route('care-schedules.index')
            ->with('success', 'Jadwal perawatan berhasil dihapus.');
    }

    private function authorizeSchedule(CareSchedule $careSchedule)
    {
        if ($careSchedule->pet->user_id !== Auth::id()) {
            abort(403);
        }
    }
}