<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Deposit;
use App\Models\DepositCategory;
use App\Observers\AccountObserver;
use App\Observers\DepositCategoryObserver;
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
    }
}
