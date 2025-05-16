<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AuthenticationController;

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
// Public API routes

Route::post('/auth/login', [AuthenticationController::class, 'login']);

// Protected API routes - using our custom guard
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthenticationController::class, 'user']);
    Route::post('/logout', [AuthenticationController::class, 'logout']);

    // Add other protected API routes here...
    Route::post('/callback', [NotificationController::class, 'index']);
});

// Traditional User Routes
Route::middleware('auth:sanctum')->get('/user-default', function (Request $request) {
    return $request->user();
});
