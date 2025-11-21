<?php
use App\Http\Controllers\Auth\ApiAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Login để lấy token
Route::post('/login', [ApiAuthController::class, 'login'])->middleware('throttle:6,1');

// Các route yêu cầu token
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [ApiAuthController::class, 'me']);
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::post('/logout-all', [ApiAuthController::class, 'logoutAll']);

    // Ví dụ một route tài nguyên cần bảo vệ:
    // Route::get('/products', [ProductController::class, 'index']);
});