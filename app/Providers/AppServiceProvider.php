<?php

namespace App\Providers;

use App\Interfaces\DerechoAseo\CuotaAseoRepositoryInterface;
use App\Interfaces\ParametrosGenerales\ParametrosGeneralesRepositoryInterface;
use App\Interfaces\PatenteMunicipal\PatenteRepositoryInterface;
use App\Interfaces\PermisoCirculacion\MaestroPermisoRepositoryInterface;
use App\Interfaces\PermisoCirculacion\MultaTransitoRepositoryInterface;
use App\Interfaces\PermisoCirculacion\RevisionRepositoryInterface;
use App\Interfaces\PermisoCirculacion\SeguroRepositoryInterface;
use App\Repositories\DerechoAseo\CuotaAseoRepository;
use App\Repositories\ParametrosGenerales\ParametrosGeneralesRepository;
use App\Repositories\PatenteMunicipal\PatenteRepository;
use App\Repositories\PermisoCirculacion\MaestroPermisoRepository;
use App\Repositories\PermisoCirculacion\MultaTransitoRepository;
use App\Repositories\PermisoCirculacion\RevisionRepository;
use App\Repositories\PermisoCirculacion\SeguroRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MaestroPermisoRepositoryInterface::class, MaestroPermisoRepository::class);
        $this->app->bind(MultaTransitoRepositoryInterface::class, MultaTransitoRepository::class);
        $this->app->bind(SeguroRepositoryInterface::class, SeguroRepository::class);
        $this->app->bind(RevisionRepositoryInterface::class, RevisionRepository::class);
        $this->app->bind(PatenteRepositoryInterface::class, PatenteRepository::class);
        $this->app->bind(CuotaAseoRepositoryInterface::class, CuotaAseoRepository::class);
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
