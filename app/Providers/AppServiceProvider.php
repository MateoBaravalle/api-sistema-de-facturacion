<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'clients' => 'App\Models\Client',
            'discounts' => 'App\Models\Discount',
            'invoices' => 'App\Models\Invoice',
            'notifications' => 'App\Models\Notification',
            'orders' => 'App\Models\Order',
            'payments' => 'App\Models\Payment',
            'products' => 'App\Models\Product',
            'roles' => 'Spatie\Permission\Models\Role',
            'suppliers' => 'App\Models\Supplier',
            'transactions' => 'App\Models\Transaction',
            'users' => 'App\Models\User',
        ]);
    }
}
