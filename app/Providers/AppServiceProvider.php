<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Deposit;
use App\Models\DepositCategory;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\UnitType;
use App\Observers\AccountObserver;
use App\Observers\AttributeObserver;
use App\Observers\BrandObserver;
use App\Observers\CategoryObserver;
use App\Observers\DepositCategoryObserver;
use App\Observers\DepositObserver;
use App\Observers\ExpenseCategoryObserver;
use App\Observers\ExpenseObserver;
use App\Observers\PaymentMethodObserver;
use App\Observers\UnitTypeObserver;
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
    }
}
