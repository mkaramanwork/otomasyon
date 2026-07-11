<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;
use App\Models\Product;
use App\Models\LabProduct;
use App\Observers\SynchronizationObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }
    public function boot()
    {
        Product::observe(SynchronizationObserver::class);
        LabProduct::observe(SynchronizationObserver::class);
    }

    
}
