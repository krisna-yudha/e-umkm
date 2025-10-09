<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\UmkmMenuController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Public routes
Route::get('/', [DashboardController::class, 'home'])->name('home');
Route::get('/umkm-list', [DashboardController::class, 'publicUmkmList'])->name('public.umkm.list');
Route::get('/umkm-public/{umkm}', [DashboardController::class, 'publicUmkmShow'])->name('public.umkm.show');
Route::get('/mapping', [DashboardController::class, 'mapping'])->name('mapping');
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/api', [ArticleController::class, 'api'])->name('articles.api');

// Password Reset Routes (Public)
Route::get('/forgot-password', function () {
    return Inertia::render('Auth/FastPasswordReset');
})->name('password.request');

// Fast Password Reset Route
Route::get('/fast-password-reset', function () {
    return Inertia::render('Auth/FastPasswordReset');
})->name('fast-password-reset');
Route::post('/forgot-password', [PasswordResetController::class, 'submitRequest'])->name('password.reset.request');
Route::get('/password-reset/verification', [PasswordResetController::class, 'showVerificationForm'])->name('password.reset.verification');
Route::post('/password-reset/verify', [PasswordResetController::class, 'verifyCode'])->name('password.reset.verify');
Route::get('/password-reset/new', [PasswordResetController::class, 'showNewPasswordForm'])->name('password.reset.new');
Route::post('/password-reset/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset.update');

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
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::get('/', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/umkm', [App\Http\Controllers\AdminController::class, 'umkmIndex'])->name('admin.umkm');
        Route::post('/umkm/{id}/toggle-status', [App\Http\Controllers\AdminController::class, 'toggleUmkmStatus'])->name('admin.umkm.toggle');
        Route::get('/reports', [App\Http\Controllers\AdminController::class, 'reports'])->name('admin.reports');
        Route::get('/search', [App\Http\Controllers\AdminController::class, 'search'])->name('admin.search');
        
        // Admin Password Reset Management
        Route::get('/password-reset-requests', [App\Http\Controllers\AdminController::class, 'passwordResetRequests'])->name('admin.password-reset-requests');
        Route::post('/password-reset/{passwordResetRequest}/approve', [App\Http\Controllers\AdminController::class, 'approvePasswordReset'])->name('admin.password-reset.approve');
        Route::post('/password-reset/{passwordResetRequest}/reject', [App\Http\Controllers\AdminController::class, 'rejectPasswordReset'])->name('admin.password-reset.reject');
    });
    
    // UMKM routes - Create accessible to all authenticated users
    Route::get('/umkm/create', [UmkmController::class, 'create'])->name('umkm.create');
    Route::post('/umkm', [UmkmController::class, 'store'])->name('umkm.store');
    Route::get('/umkm', [UmkmController::class, 'index'])->name('umkm.index');
    Route::get('/umkm/{umkm}', [UmkmController::class, 'show'])->name('umkm.show');
    Route::get('/umkm/{umkm}/edit', [UmkmController::class, 'edit'])->name('umkm.edit');
    Route::put('/umkm/{umkm}', [UmkmController::class, 'update'])->name('umkm.update');
    Route::patch('/umkm/{umkm}', [UmkmController::class, 'update']);
    Route::patch('/umkm/{umkm}/toggle-status', [UmkmController::class, 'toggleStatus'])->name('umkm.toggleStatus');
    Route::delete('/umkm/{umkm}', [UmkmController::class, 'destroy'])->name('umkm.destroy');
    
    // UMKM Menu routes
    Route::resource('umkm.menu', UmkmMenuController::class)->parameters([
        'umkm' => 'umkm',
        'menu' => 'menu'
    ]);
});

require __DIR__.'/auth.php';
