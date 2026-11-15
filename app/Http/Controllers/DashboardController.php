<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\CareSchedule;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $pets = Pet::where('user_id', $userId)->get();
        $petIds = $pets->pluck('id');

        $schedules = CareSchedule::whereIn('pet_id', $petIds)
            ->orderBy('scheduled_date', 'asc')
            ->get();

        $monthlyExpense = Expense::whereIn('pet_id', $petIds)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');

        return view('dashboard', compact('pets', 'schedules', 'monthlyExpense'));
    }
}