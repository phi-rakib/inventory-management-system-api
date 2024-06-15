<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdjustmentController;
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
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitTypeController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('accounts/restore/{id}', [AccountController::class, 'restore'])->name('accounts.restore');
    Route::delete('accounts/forceDelete/{id}', [AccountController::class, 'forceDelete'])->name('accounts.forceDelete');
    Route::apiResource('accounts', AccountController::class);

    Route::get('depositCategories/restore/{id}', [DepositCategoryController::class, 'restore'])->name('depositCategories.restore');
    Route::delete('depositCategories/forceDelete/{id}', [DepositCategoryController::class, 'forceDelete'])->name('depositCategories.forceDelete');
    Route::apiResource('depositCategories', DepositCategoryController::class);

    Route::get('deposits/restore/{id}', [DepositController::class, 'restore'])->name('deposits.restore');
    Route::delete('deposits/forceDelete/{id}', [DepositController::class, 'forceDelete'])->name('deposits.forceDelete');
    Route::apiResource('deposits', DepositController::class);

    Route::get('paymentMethods/restore/{id}', [PaymentMethodController::class, 'restore'])->name('paymentMethods.restore');
    Route::delete('paymentMethods/forceDelete/{id}', [PaymentMethodController::class, 'forceDelete'])->name('paymentMethods.forceDelete');
    Route::apiResource('paymentMethods', PaymentMethodController::class);

    Route::get('expenseCategories/restore/{id}', [ExpenseCategoryController::class, 'restore'])->name('expenseCategories.restore');
    Route::delete('expenseCategories/forceDelete/{id}', [ExpenseCategoryController::class, 'forceDelete'])->name('expenseCategories.forceDelete');
    Route::apiResource('expenseCategories', ExpenseCategoryController::class);

    Route::get('expenses/restore/{id}', [ExpenseController::class, 'restore'])->name('expenses.restore');
    Route::delete('expenses/forceDelete/{id}', [ExpenseController::class, 'forceDelete'])->name('expenses.forceDelete');
    Route::apiResource('expenses', ExpenseController::class);

    Route::get('brands/restore/{id}', [BrandController::class, 'restore'])->name('brands.restore');
    Route::delete('brands/forceDelete/{id}', [BrandController::class, 'forceDelete'])->name('brands.forceDelete');
    Route::apiResource('brands', BrandController::class);

    Route::get('categories/restore/{id}', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('categories/forceDelete/{id}', [CategoryController::class, 'forceDelete'])->name('categories.forceDelete');
    Route::apiResource('categories', CategoryController::class);

    Route::get('unitTypes/restore/{id}', [UnitTypeController::class, 'restore'])->name('unitTypes.restore');
    Route::delete('unitTypes/forceDelete/{id}', [UnitTypeController::class, 'forceDelete'])->name('unitTypes.forceDelete');
    Route::apiResource('unitTypes', UnitTypeController::class);

    Route::delete('attributes/forceDelete/{id}', [AttributeController::class, 'forceDelete'])->name('attributes.forceDelete');
    Route::get('attributes/restore/{id}', [AttributeController::class, 'restore'])->name('attributes.restore');
    Route::apiResource('attributes', AttributeController::class);

    Route::get('attributeValues/restore/{id}', [AttributeValueController::class, 'restore'])->name('attributeValues.restore');
    Route::delete('attributeValues/forceDelete/{id}', [AttributeValueController::class, 'forceDelete'])->name('attributeValues.forceDelete');
    Route::apiResource('attributeValues', AttributeValueController::class);

    Route::apiResource('products', ProductController::class);

    Route::get('warehouses/restore/{id}', [WarehouseController::class, 'restore'])->name('warehouses.restore');
    Route::delete('warehouses/forceDelete/{id}', [WarehouseController::class, 'forceDelete'])->name('warehouses.forceDelete');
    Route::apiResource('warehouses', WarehouseController::class);

    Route::apiResource('adjustments', AdjustmentController::class);

    Route::get('suppliers/restore/{id}', [SupplierController::class, 'restore'])->name('suppliers.restore');
    Route::delete('suppliers/forceDelete/{id}', [SupplierController::class, 'forceDelete'])->name('suppliers.forceDelete');
    Route::apiResource('suppliers', SupplierController::class);
});
