<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group( function () {
    // Profile
    Route::get('/user', function (Request $request) {
        return response()->json([
            'message' => 'Success',
            'data' => $request->user()
        ]);
    });

    // Invoice
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::get('/invoice/{uuid}', [InvoiceController::class, 'show']);
    Route::post('/invoice', [InvoiceController::class, 'store']);
    Route::post('/invoice/payment/{uuid}', [InvoiceController::class, 'payment']);
    Route::post('/invoice/notification', [InvoiceController::class, 'notification']);
    Route::put('/invoice/{uuid}', [InvoiceController::class, 'update']);
    Route::delete('/invoice/{uuid}', [InvoiceController::class, 'destroy']);

    // Customer
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customer/{uuid}', [CustomerController::class, 'show']);
    Route::post('/customer', [CustomerController::class, 'store']);
    Route::put('/customer/{uuid}', [CustomerController::class, 'update']);
    Route::delete('/customer/{uuid}', [CustomerController::class, 'destroy']);
});
