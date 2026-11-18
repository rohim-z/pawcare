<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\WeightLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PetController extends Controller
{
    public function index()
    {
        $pets = Pet::with(['schedules', 'expenses', 'weightLogs'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('pets.index', compact('pets'));
    }

    public function create()
    {
        return view('pets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'species' => 'required|string|max:50',
            'breed' => 'nullable|string|max:100',
            'gender' => 'required|in:Jantan,Betina',
            'birth_date' => 'nullable|date',
            'weight' => 'nullable|numeric|min:0|max:200',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        $weight = $validated['weight'] ?? null;
        unset($validated['weight']);

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('pets', 'public');
        }

        $pet = Pet::create($validated);

        if ($weight !== null) {
            WeightLog::create([
                'pet_id' => $pet->id,
                'weight' => $weight,
                'recorded_date' => now()->toDateString(),
            ]);
        }

        return redirect()->route('pets.index')->with('success', 'Data hewan berhasil ditambahkan.');
    }

    public function show(Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            abort(403);
        }

        return view('pets.show', compact('pet'));
    }

    public function edit(Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            abort(403);
        }

        return view('pets.edit', compact('pet'));
    }

    public function update(Request $request, Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'species' => 'required|string|max:50',
            'breed' => 'nullable|string|max:100',
            'gender' => 'required|in:Jantan,Betina',
            'birth_date' => 'nullable|date',
            'weight' => 'nullable|numeric|min:0|max:200',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        $weight = $validated['weight'] ?? null;
        unset($validated['weight']);

        if ($request->hasFile('photo')) {
            if ($pet->photo) {
                Storage::disk('public')->delete($pet->photo);
            }
            $validated['photo'] = $request->file('photo')->store('pets', 'public');
        }

        $pet->update($validated);

        if ($weight !== null) {
            WeightLog::create([
                'pet_id' => $pet->id,
                'weight' => $weight,
                'recorded_date' => now()->toDateString(),
            ]);
        }

        return redirect()->route('pets.index')->with('success', 'Data hewan berhasil diperbarui.');
    }

    public function destroy(Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            abort(403);
        }

        if ($pet->photo) {
            Storage::disk('public')->delete($pet->photo);
        }

        $pet->delete();

        return redirect()->route('pets.index')->with('success', 'Data hewan berhasil dihapus.');
    }
}