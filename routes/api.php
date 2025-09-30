<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\UmkmApiController;
use App\Http\Controllers\Api\MenuApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthApiController::class, 'register']);
        Route::post('/login', [AuthApiController::class, 'login']);
    });

    // Public UMKM routes
    Route::prefix('umkm')->group(function () {
        Route::get('/', [UmkmApiController::class, 'index']);
        Route::get('/{id}', [UmkmApiController::class, 'show']);
        Route::get('/statistics/data', [UmkmApiController::class, 'statistics']);
        Route::post('/nearby', [UmkmApiController::class, 'nearby']);
    });

    // Public Menu routes
    Route::prefix('menus')->group(function () {
        Route::get('/', [MenuApiController::class, 'allMenus']);
        Route::get('/umkm/{umkmId}', [MenuApiController::class, 'index']);
        Route::get('/umkm/{umkmId}/{menuId}', [MenuApiController::class, 'show']);
    });
});

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    
    // Auth profile routes
    Route::prefix('auth')->group(function () {
        Route::get('/profile', [AuthApiController::class, 'profile']);
        Route::put('/profile', [AuthApiController::class, 'updateProfile']);
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::post('/logout-all', [AuthApiController::class, 'logoutAll']);
    });

    // Protected UMKM routes
    Route::prefix('umkm')->group(function () {
        Route::post('/', [UmkmApiController::class, 'store']);
        Route::put('/{id}', [UmkmApiController::class, 'update']);
        Route::delete('/{id}', [UmkmApiController::class, 'destroy']);
    });

    // Protected Menu routes
    Route::prefix('menus')->group(function () {
        Route::post('/umkm/{umkmId}', [MenuApiController::class, 'store']);
        Route::put('/umkm/{umkmId}/{menuId}', [MenuApiController::class, 'update']);
        Route::delete('/umkm/{umkmId}/{menuId}', [MenuApiController::class, 'destroy']);
    });
});

// Health check route
Route::get('/health', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'E-UMKM API is running',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});