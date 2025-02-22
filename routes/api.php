<?php

use App\Http\Controllers\AuthController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\DeliveryAddressController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});



Route::post('/delivery-address', [DeliveryAddressController::class, 'store']); // Public route for storing

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/delivery-address/{id}', [DeliveryAddressController::class, 'show']); // Get a single record
    Route::get('/delivery-address', [DeliveryAddressController::class, 'index']); // List with filters
});
