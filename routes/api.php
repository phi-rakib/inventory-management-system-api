<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeValueController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepositCategoryController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\UnitTypeController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('accounts', AccountController::class);
    Route::apiResource('depositCategories', DepositCategoryController::class);
    Route::apiResource('deposits', DepositController::class);
    Route::apiResource('paymentMethods', PaymentMethodController::class);
    Route::apiResource('expenseCategories', ExpenseCategoryController::class);
    Route::apiResource('expenses', ExpenseController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('unitTypes', UnitTypeController::class);
    Route::apiResource('attributes', AttributeController::class);
    Route::apiResource('attributeValues', AttributeValueController::class);
    Route::apiResource('warehouses', WarehouseController::class);
});
