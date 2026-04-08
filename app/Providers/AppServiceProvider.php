<?php

namespace App\Providers;

use App\Models\ProProject;
use App\Models\ProjectUsage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Use Bootstrap 5 pagination
        Paginator::useBootstrapFive();

        // Bind {project} route param to ProProject model
        Route::model('project',    ProProject::class);
        Route::model('usage',      ProjectUsage::class);
        Route::model('assignment', \App\Models\UsageSession::class);
    }
}
