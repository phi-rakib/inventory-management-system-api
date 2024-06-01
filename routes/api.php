<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepositCategoryController;
use App\Http\Controllers\DepositController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('accounts', AccountController::class);
    Route::apiResource('depositCategories', DepositCategoryController::class);
    Route::apiResource('deposits', DepositController::class);
});
