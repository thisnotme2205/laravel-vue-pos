<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('categories', CategoryController::class);

Route::get('product', [ProductController::class, 'product']);
Route::apiResource('products', ProductController::class);

Route::apiResource('cart', CartController::class);
Route::apiResource('transactions', TransactionController::class);
Route::get('sum-transaction', [CartController::class, 'sumTransaction']);