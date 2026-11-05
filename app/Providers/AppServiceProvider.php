<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Shipment;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\Destination;
use App\Observers\ActivityLogObserver;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register model observers

        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Shipment::observe(ActivityLogObserver::class);
        Driver::observe(ActivityLogObserver::class);
        Truck::observe(ActivityLogObserver::class);
        Destination::observe(ActivityLogObserver::class);
        Paginator::useBootstrap();
    }
}
