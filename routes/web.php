<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UmkmController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Public routes
Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');
Route::get('/umkm-list', [DashboardController::class, 'publicUmkmList'])->name('public.umkm.list');
Route::get('/mapping', [DashboardController::class, 'mapping'])->name('public.mapping');

// Protected routes
Route::get('/dashboard', function () {
    if (Auth::user()->role === 'umkm') {
        return redirect()->route('umkm.index');
    }
    return redirect()->route('public.umkm.list');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // UMKM routes (only for UMKM role)
    Route::middleware(['role:umkm'])->group(function () {
        Route::resource('umkm', UmkmController::class);
    });
});

require __DIR__.'/auth.php';
