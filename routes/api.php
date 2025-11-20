<?php

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PartnerJoinRequestController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PartnerReviewController;
use App\Http\Controllers\CategoryController;

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

Route::prefix('admin')->group(function () {
    Route::post('/register', [PartnerController::class, 'register']);
    Route::post('/login', [PartnerController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [PartnerController::class, 'logout']);

        Route::get('/me', [PartnerController::class, 'me']);
        Route::post('/me', [PartnerController::class, 'edit']);

        Route::post('/products', [ProductController::class, 'store']); // Қосу
        Route::post('/products/{product}', [ProductController::class, 'update']); // Өзгерту
        Route::delete('/products/{product}', [ProductController::class, 'destroy']); // Жою
        Route::post('/products/{product}/approve', [ProductController::class, 'approve']); // мадерация продукт

        Route::post('/products/{product}/images', [ProductController::class, 'uploadImage']); // сурет қосу
        Route::delete('/product/images/{image}', [ProductController::class, 'deleteImage']);  // сурет өшіру

        Route::post('/products/{product}/request-activation', [ProductController::class, 'requestActivation']); // запрос мадерация продукт
        Route::get('/activation-requests', [ProductController::class, 'activationRequests']); // запрос тізімі
        Route::post('/products/{product}/approve', [ProductController::class, 'approve']); // қабылдау
        Route::post('/products/{product}/reject', [ProductController::class, 'reject']);  // отказ

        Route::post('/products/{product}/join-request', [PartnerJoinRequestController::class, 'sendJoinRequest']); // дайын продуктқа қосылу
        Route::get('/partner-join-requests', [PartnerJoinRequestController::class, 'index']); // қосылмақшы болғандар тізімі
        Route::post('/partner-join-requests/{request}/approve', [PartnerJoinRequestController::class, 'approve']); // қабылдау
        Route::post('/partner-join-requests/{request}/reject', [PartnerJoinRequestController::class, 'reject']); // отказ

        Route::middleware('admin')->group(function () {
        });
    });


    Route::get('/partners', [PartnerController::class, 'index']);
    Route::post('/partners', [PartnerController::class, 'store']);
    Route::post('/partners/{id}', [PartnerController::class, 'update']);
    Route::delete('/partners/{id}', [PartnerController::class, 'destroy']);


    Route::post('/products/{id}/partners', [ProductController::class, 'attachPartner']);
});
