<?php

namespace App\Providers;

use App\Interfaces\ParametrosGenerales\ParametrosGeneralesRepositoryInterface;
use App\Interfaces\PermisoCirculacion\MaestroPermisoRepositoryInterface;
use App\Repositories\ParametrosGenerales\ParametrosGeneralesRepository;
use App\Repositories\PermisoCirculacion\MaestroPermisoRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MaestroPermisoRepositoryInterface::class, MaestroPermisoRepository::class);
        $this->app->bind(ParametrosGeneralesRepositoryInterface::class, ParametrosGeneralesRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
