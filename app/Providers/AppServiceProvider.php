<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

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
        // Prevent index length issues on utf8mb4 with older MariaDB defaults.
        Schema::defaultStringLength(191);

        if (app()->isLocal()) {
            Model::preventLazyLoading();
        }
    }
}
