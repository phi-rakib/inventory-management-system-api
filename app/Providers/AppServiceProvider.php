<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Account;
use App\Models\Adjustment;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Deposit;
use App\Models\DepositCategory;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\UnitType;
use App\Models\Warehouse;
use App\Observers\AccountObserver;
use App\Observers\AdjustmentObserver;
use App\Observers\AttributeObserver;
use App\Observers\AttributeValueObserver;
use App\Observers\BrandObserver;
use App\Observers\CategoryObserver;
use App\Observers\DepositCategoryObserver;
use App\Observers\DepositObserver;
use App\Observers\ExpenseCategoryObserver;
use App\Observers\ExpenseObserver;
use App\Observers\PaymentMethodObserver;
use App\Observers\ProductObserver;
use App\Observers\SupplierObserver;
use App\Observers\UnitTypeObserver;
use App\Observers\WarehouseObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Account::observe(AccountObserver::class);
        DepositCategory::observe(DepositCategoryObserver::class);
        Deposit::observe(DepositObserver::class);
        PaymentMethod::observe(PaymentMethodObserver::class);
        ExpenseCategory::observe(ExpenseCategoryObserver::class);
        Expense::observe(ExpenseObserver::class);
        Brand::observe(BrandObserver::class);
        Category::observe(CategoryObserver::class);
        UnitType::observe(UnitTypeObserver::class);
        Attribute::observe(AttributeObserver::class);
        AttributeValue::observe(AttributeValueObserver::class);
        Product::observe(ProductObserver::class);
        Warehouse::observe(WarehouseObserver::class);
        Adjustment::observe(AdjustmentObserver::class);
        Supplier::observe(SupplierObserver::class);
    }
}
