<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::middleware(['auth:sanctum', 'role:admin'])->get('/admin-only', function () {
    return response()->json(['message' => 'Welcome Admin']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users', [UserController::class, 'index']); // Hanya Admin
    Route::post('/users', [UserController::class, 'store']); // Hanya Admin
    Route::get('/users/{user}', [UserController::class, 'show']); // Admin atau Diri Sendiri
    Route::put('/users/{user}', [UserController::class, 'update']); // Admin atau Diri Sendiri
    Route::delete('/users/{user}', [UserController::class, 'destroy']); // Hanya Admin
});