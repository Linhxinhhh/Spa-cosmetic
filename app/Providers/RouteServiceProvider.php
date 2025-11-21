<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Models\Order;
use App\Observers\OrderObserver;
class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/users/home';            // cho user
    public const ADMIN_HOME = '/admin/dashboard'; // cho admin
    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api_force.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
        Order::observe(OrderObserver::class);
    }

    
}
