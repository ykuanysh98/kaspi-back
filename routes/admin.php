<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PartnerJoinRequestController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group with "api/admin" prefix.
|
*/

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
    Route::post('/products/{product}/remove', [PartnerJoinRequestController::class, 'remove']); // дайын продуктқа қосылу
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
