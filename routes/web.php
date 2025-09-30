<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\UmkmMenuController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Public routes
Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');
Route::get('/umkm-list', [DashboardController::class, 'publicUmkmList'])->name('public.umkm.list');
Route::get('/umkm-public/{umkm}', [DashboardController::class, 'publicUmkmShow'])->name('public.umkm.show');
Route::get('/mapping', [DashboardController::class, 'mapping'])->name('mapping');

// Protected routes
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User Profile Route
    Route::get('/user-profile', [UserProfileController::class, 'show'])->name('user.profile');
    
    // Help Route
    Route::get('/help', function () {
        return Inertia::render('Help');
    })->name('help');
    
    // Admin routes - Only accessible to admin users  
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/umkm', [App\Http\Controllers\AdminController::class, 'umkmIndex'])->name('admin.umkm');
    Route::post('/admin/umkm/{id}/toggle-status', [App\Http\Controllers\AdminController::class, 'toggleUmkmStatus'])->name('admin.umkm.toggle');
    Route::get('/admin/reports', [App\Http\Controllers\AdminController::class, 'reports'])->name('admin.reports');
    
    // UMKM routes - Create accessible to all authenticated users
    Route::get('/umkm/create', [UmkmController::class, 'create'])->name('umkm.create');
    Route::post('/umkm', [UmkmController::class, 'store'])->name('umkm.store');
    Route::get('/umkm', [UmkmController::class, 'index'])->name('umkm.index');
    Route::get('/umkm/{umkm}', [UmkmController::class, 'show'])->name('umkm.show');
    Route::get('/umkm/{umkm}/edit', [UmkmController::class, 'edit'])->name('umkm.edit');
    Route::put('/umkm/{umkm}', [UmkmController::class, 'update'])->name('umkm.update');
    Route::patch('/umkm/{umkm}', [UmkmController::class, 'update'])->name('umkm.update');
    Route::delete('/umkm/{umkm}', [UmkmController::class, 'destroy'])->name('umkm.destroy');
    
    // UMKM Menu routes
    Route::resource('umkm.menu', UmkmMenuController::class)->parameters([
        'umkm' => 'umkm',
        'menu' => 'menu'
    ]);
});

require __DIR__.'/auth.php';
