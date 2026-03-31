<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\UserModuleRepository;
use App\Repositories\UserModuleRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserModuleRepositoryInterface::class,UserModuleRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
