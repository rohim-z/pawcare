<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\CareScheduleController;

// Jika user membuka halaman utama langsung diarahkan ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// yang sudah login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // my pets
    Route::resource('pets', PetController::class);

    // health records
    Route::resource('health-records', HealthRecordController::class)->except(['show']);

    // care schedules
    Route::resource('care-schedules', CareScheduleController::class)->except(['show']);
    Route::patch('/care-schedules/{careSchedule}/toggle', [CareScheduleController::class, 'toggleComplete'])
    ->name('care-schedules.toggle');
});