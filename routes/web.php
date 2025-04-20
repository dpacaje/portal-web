<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermisoCirculacion\PermisoCirculacionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Route::get('/Administracion', function () {
    return view('Admin.dashboard');
});

Route::prefix('permisocirculacion')->group(function () {
    Route::get('/', [PermisoCirculacionController::class, 'index'])->name('permisocirculacion.index');
    Route::post('/deuda', [PermisoCirculacionController::class, 'deuda'])->name('permisocirculacion.deuda');
});