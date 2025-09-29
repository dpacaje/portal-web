<?php

use App\Http\Controllers\DerechoAseo\CuotaAseoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatenteMunicipal\PatenteMunicipalController;
use App\Http\Controllers\PermisoCirculacion\PermisoCirculacionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('PermisoCirculacion')->group(function () {
    Route::get('/', [PermisoCirculacionController::class, 'index'])->name('permisocirculacion.index');
    Route::post('ObtenerDeuda', [PermisoCirculacionController::class, 'deuda'])->name('permisocirculacion.deuda');
    Route::post('Confirmacion', [PermisoCirculacionController::class, 'confirmacion'])->name('permisocirculacion.confirmacion');
    // Route::get('Reimprime', [PermisoCirculacionController::class, 'reimprime'])->name('permisocirculacion.reimprime');
    // Route::post('HistorialPagos', [PermisoCirculacionController::class, 'historial'])->name('permisocirculacion.historial');
});

Route::prefix('PatenteMunicipal')->group(function () {
    Route::get('/', [PatenteMunicipalController::class, 'index'])->name('patentemunicipal.index');
    Route::post('ObtenerDeuda', [PatenteMunicipalController::class, 'deuda'])->name('patentemunicipal.deuda');
    // Route::get('Reimprime', [PatenteMunicipalController::class, 'reimprime'])->name('patentemunicipal.reimprime');
    // Route::post('HistorialPagos', [PatenteMunicipalController::class, 'historial'])->name('patentemunicipal.historial');
});

Route::prefix('DerechoAseo')->group(function () {
    Route::get('/', [CuotaAseoController::class, 'index'])->name('derechoaseo.index');
    Route::post('ObtenerDeuda', [CuotaAseoController::class, 'deuda'])->name('derechoaseo.deuda');
    // Route::get('Reimprime', [CuotaAseoController::class, 'reimprime'])->name('derechoaseo.reimprime');
    // Route::post('HistorialPagos', [CuotaAseoController::class, 'historial'])->name('derechoaseo.historial');
});

Route::prefix('PagoOnline')->group(function () {
    Route::post('Generar', [PermisoCirculacionController::class, 'deuda']);
    Route::get('Resultado', [PermisoCirculacionController::class, 'deuda']);
});

Route::prefix('Documentos')->group(function () {
    Route::post('GenerarPermiso', [PermisoCirculacionController::class, 'deuda']);
    Route::post('GenerarMulta', [PermisoCirculacionController::class, 'deuda']);
    Route::post('GenerarPatente', [PermisoCirculacionController::class, 'deuda']);
    Route::post('GenerarAseo', [PermisoCirculacionController::class, 'deuda']);
});
