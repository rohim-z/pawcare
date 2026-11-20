<?php

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HealthRecordController extends Controller
{
    public function index(Request $request)
    {
        $pets = Pet::where('user_id', Auth::id())->orderBy('name')->get();
        $petIds = $pets->pluck('id');

        $query = HealthRecord::with('pet')
            ->whereIn('pet_id', $petIds)
            ->orderByDesc('record_date')
            ->orderByDesc('id');

        if ($request->filled('pet_id')) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->filled('record_type')) {
            $query->where('record_type', $request->record_type);
        }

        $records = $query->get();

        return view('health_records.index', compact('records', 'pets'));
    }

    public function create(Request $request)
    {
        $pets = Pet::where('user_id', Auth::id())->orderBy('name')->get();
        $selectedPetId = $request->query('pet_id');

        return view('health_records.create', compact('pets', 'selectedPetId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id'             => 'required|exists:pets,id',
            'record_type'        => 'required|string|max:100',
            'title'              => 'required|string|max:150',
            'record_date'        => 'required|date',
            'vet_name'           => 'nullable|string|max:150',
            'diagnosis_or_notes' => 'nullable|string',
            'next_visit_date'    => 'nullable|date|after_or_equal:record_date',
        ]);

        // Pastikan pet milik user yang login
        Pet::where('id', $validated['pet_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        HealthRecord::create($validated);

        return redirect()->route('health-records.index')
            ->with('success', 'Rekam medis berhasil ditambahkan.');
    }

    public function edit(HealthRecord $healthRecord)
    {
        abort_if($healthRecord->pet->user_id !== Auth::id(), 403);

        $pets = Pet::where('user_id', Auth::id())->orderBy('name')->get();

        return view('health_records.edit', compact('healthRecord', 'pets'));
    }

    public function update(Request $request, HealthRecord $healthRecord)
    {
        abort_if($healthRecord->pet->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'pet_id'             => 'required|exists:pets,id',
            'record_type'        => 'required|string|max:100',
            'title'              => 'required|string|max:150',
            'record_date'        => 'required|date',
            'vet_name'           => 'nullable|string|max:150',
            'diagnosis_or_notes' => 'nullable|string',
            'next_visit_date'    => 'nullable|date|after_or_equal:record_date',
        ]);

        Pet::where('id', $validated['pet_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $healthRecord->update($validated);

        return redirect()->route('health-records.index')
            ->with('success', 'Rekam medis berhasil diperbarui.');
    }

    public function destroy(HealthRecord $healthRecord)
    {
        abort_if($healthRecord->pet->user_id !== Auth::id(), 403);

        $healthRecord->delete();

        return redirect()->route('health-records.index')
            ->with('success', 'Rekam medis berhasil dihapus.');
    }
}