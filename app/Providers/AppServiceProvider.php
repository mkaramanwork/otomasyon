<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
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

        Gate::define('manage-lab-report-settings', function ($user) {
            return $user->hasAnyRole([
                'admin',
                'laboratuvarmüdürü',
                'laboratuvaryoneticisi',
            ]);
        });

        Gate::define('approve-lab-reports', function ($user) {
            return $user->hasAnyRole([
                'laboratuvarmüdürü',
                'laboratuvaryoneticisi',
            ]);
        });
    }
}