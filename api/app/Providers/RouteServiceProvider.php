<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {

            // 🔵 Routes API (stateless, sans CSRF)
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/api.php'));

            // 🟢 Routes WEB (session, cookies, CSRF)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
