<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Appointment;

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
Route::get('/api/availability', function (Request $r) {
    $serviceId = $r->query('service_id');
    $date = $r->query('date');
    if (!$serviceId || !$date) return response()->json([]);

    $taken = \App\Models\Appointment::where('service_id', $serviceId)
        ->whereDate('appointment_date', $date)
        ->whereIn('status', ['pending','confirmed'])
        ->pluck('start_time')
        ->map(fn($t) => substr($t, 0, 5))  // ✅ 12:00:00 -> 12:00
        ->values();

    return response()->json($taken);
});

