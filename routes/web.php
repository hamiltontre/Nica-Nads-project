<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AtletaController;
use App\Http\Controllers\AsistenciaController;

// Autenticación
Route::match(['get', 'post'], '/login', [AuthController::class, 'handleAuth'])->name('login');

// Grupo de rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard principal (redirige a atletas por ahora)
    Route::get('/', function () {
        return redirect()->route('atletas.index');
    });
    
    Route::resource('atletas', AtletaController::class);
    
Route::get('/atletas/por-grupo/{grupo}', [AtletaController::class, 'porGrupo'])
    ->name('atletas.porGrupo')
    ->where('grupo', 'Federados|Novatos|Juniors|Principiantes');

    Route::get('/atletas/{atleta}/edit', [AtletaController::class, 'edit'])->name('atletas.edit');

Route::resource('facturas', \App\Http\Controllers\FacturaController::class)
    ->middleware('auth'); // Opcional: proteger con autenticación

 // Rutas de asistencias
    Route::get('/asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');
    Route::post('/asistencias', [AsistenciaController::class, 'store'])->name('asistencias.store');
    Route::post('/asistencias/cerrar', [AsistenciaController::class, 'cerrarMes'])->name('asistencias.cerrar');
    
    Route::get('/asistencias/historico', [AsistenciaController::class, 'historico'])
     ->name('asistencias.historico');
});