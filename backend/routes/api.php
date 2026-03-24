<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1 (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /**
     * 1. Public Routes (No Auth Required)
     */
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/medicines', [MedicineController::class, 'index']);
    Route::get('/medicines/{id}', [MedicineController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/search', [MedicineController::class, 'search']);

    /**
     * 2. Protected Routes (Must be Logged In)
     */
    Route::middleware('auth:sanctum')->group(function () {
        
        // --- Customer Actions ---
        Route::get('/user', function (Request $request) {
            return response()->json([
                'status' => 'success',
                'user' => $request->user()
            ]);
        });

        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/my-history', [OrderController::class, 'myOrders']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // --- Admin Actions ---
        Route::prefix('admin')->group(function () {
            Route::get('/stats', [AdminController::class, 'getDashboardStats']);
            Route::get('/customers', [AdminController::class, 'listCustomers']);
            Route::put('/customers/{id}/status', [AdminController::class, 'updateCustomerStatus']);
            Route::delete('/customers/{id}', [AdminController::class, 'deleteCustomer']);
        });
    });
});