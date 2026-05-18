<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\UmkmApiController;
use App\Http\Controllers\Api\MenuApiController;
use App\Http\Controllers\Api\MapApiController;
use App\Http\Controllers\Api\PasswordResetApiController;
use App\Http\Controllers\Api\UserContributionController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\WishlistController;
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

// Password Reset API
Route::get('/check-reset-request', [PasswordResetApiController::class, 'checkResetStatus']);

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

    // Public Map routes
    Route::prefix('map')->group(function () {
        Route::get('/locations', [MapApiController::class, 'getAllLocations']);
        Route::post('/locations/radius', [MapApiController::class, 'getLocationsByRadius']);
        Route::get('/categories', [MapApiController::class, 'getCategories']);
        Route::get('/statistics', [MapApiController::class, 'getMapStatistics']);
        Route::get('/location/{id}', [MapApiController::class, 'getLocationDetails']);
    });

    // Public Rating routes
    Route::prefix('umkms')->group(function () {
        Route::get('/{umkm}/ratings', [RatingController::class, 'index']);
        Route::get('/{umkm}/wishlist/check', [WishlistController::class, 'check']);
    });
});

// Protected Sanctum routes (authentication required - Token Based)
Route::middleware(['auth:sanctum', 'api'])->prefix('v1')->group(function () {
    
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
        Route::post('/{id}/toggle-status', [UmkmApiController::class, 'toggleStatus']);
    });

    // Protected Menu routes
    Route::prefix('menus')->group(function () {
        Route::post('/umkm/{umkmId}', [MenuApiController::class, 'store']);
        Route::put('/umkm/{umkmId}/{menuId}', [MenuApiController::class, 'update']);
        Route::delete('/umkm/{umkmId}/{menuId}', [MenuApiController::class, 'destroy']);
    });

    // Wishlist endpoints
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index']);
    });

    // User Contribution routes
    Route::prefix('user')->group(function () {
        Route::get('/ratings', [UserContributionController::class, 'getUserRatings']);
        Route::get('/wishlist', [UserContributionController::class, 'getUserWishlist']);
        Route::get('/stats', [UserContributionController::class, 'getContributionStats']);
    });
});

// Protected Session Auth routes (for web-based users with cookies)
// Middleware order is critical:
// 1. StartSession FIRST to load session from cookie
// 2. Then auth check to verify user
Route::middleware([
    \Illuminate\Session\Middleware\StartSession::class,
    \App\Http\Middleware\AuthenticateWithSession::class,
])->prefix('v1')->group(function () {
    // Rating routes (support session auth for public users)
    Route::prefix('umkms')->group(function () {
        Route::post('/{umkm}/ratings', [RatingController::class, 'store']);
        Route::post('/{umkm}/wishlist', [WishlistController::class, 'store']);
        Route::delete('/{umkm}/wishlist', [WishlistController::class, 'destroy']);
    });

    Route::prefix('ratings')->group(function () {
        Route::delete('/{rating}', [RatingController::class, 'destroy']);
        Route::post('/{rating}/helpful', [RatingController::class, 'markHelpful']);
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

// DEBUG: Session check endpoint (with session middleware)
Route::middleware([
    \Illuminate\Session\Middleware\StartSession::class,
])->get('/debug/session', function (Request $request) {
    return response()->json([
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'auth_check_web' => \Illuminate\Support\Facades\Auth::guard('web')->check(),
        'auth_user' => \Illuminate\Support\Facades\Auth::user(),
        'cookies' => [
            'LARAVEL_SESSION' => $request->cookie('LARAVEL_SESSION') ? substr($request->cookie('LARAVEL_SESSION'), 0, 20) . '...' : null,
            'all_cookie_names' => array_keys($request->cookies->all()),
        ],
    ]);
});