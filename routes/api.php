<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CoffeeShopController;

Route::post('posts', [PostController::class, 'store']);
// Route untuk mendapatkan data user (hanya jika menggunakan autentikasi)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route untuk Posts (CRUD)
Route::apiResource('/posts', PostController::class);

// Route untuk Coffee Shops (CRUD)
Route::apiResource('/coffee-shops', CoffeeShopController::class);

Route::put('/posts/{id}', [PostController::class, 'update']);
Route::delete('/posts/{id}', [PostController::class, 'destroy']);