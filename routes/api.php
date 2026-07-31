<?php

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PartnerReviewController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SpeechController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/users/{user}', [UserController::class, 'show']); // Белгілі қолданушы

Route::post('/chat', [ChatController::class, 'handle']);
Route::post('/transcribe', [SpeechController::class, 'transcribe']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', [UserController::class, 'me']); // Инфо
    Route::post('/user', [UserController::class, 'user']); // Өзгерту

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::post('/cart/decrease', [CartController::class, 'decrease']);
    Route::post('/merge-cart', [CartController::class, 'mergeGuestCart']);

    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders-partner', [OrderController::class, 'list']);

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index']); // Барлық қолданушылар
        Route::post('/users', [UserController::class, 'store']); // Қолданушы қосу
        Route::post('/users/{user}', [UserController::class, 'update']); // Қолданушыны жаңарту
        Route::delete('/users/{user}', [UserController::class, 'destroy']); // Қолданушыны Жою
    });

    Route::get('/partners/{id}', [PartnerController::class, 'show']);
    Route::post('/partners/{id}/review', [PartnerReviewController::class, 'store']);
    Route::get('/partners/{id}/reviews', [PartnerReviewController::class, 'index']);
});
