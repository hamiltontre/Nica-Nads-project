<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AtletaController;
use App\Http\Controllers\AsistenciaController;

Route::get('/', function () {
    return Auth::check() 
        ? redirect()->route('atletas.index') 
        : redirect()->route('login');
});


// Autenticación
Route::match(['get', 'post'], '/login', [AuthController::class, 'handleAuth'])->name('login');

// Resto de tus rutas...
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Rutas para atletas
    Route::prefix('atletas')->group(function () {
        Route::get('/', [AtletaController::class, 'index'])->name('atletas.index');
        Route::get('/create', [AtletaController::class, 'create'])->name('atletas.create');
        Route::post('/', [AtletaController::class, 'store'])->name('atletas.store');
        Route::get('/{atleta}/edit', [AtletaController::class, 'edit'])->name('atletas.edit');
        Route::put('/{atleta}', [AtletaController::class, 'update'])->name('atletas.update');
        Route::delete('/{atleta}', [AtletaController::class, 'destroy'])->name('atletas.destroy');
        
        // Nueva ruta para cargar atletas por grupo (AJAX)
        Route::get('/grupo/{grupo}', [AtletaController::class, 'getAtletasByGrupo'])
             ->name('atletas.byGrupo');
    });

Route::resource('facturas', \App\Http\Controllers\FacturaController::class)
    ->middleware('auth'); // Opcional: proteger con autenticación

 // Rutas de asistencias
    Route::get('/asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');
    Route::post('/asistencias', [AsistenciaController::class, 'store'])->name('asistencias.store');
    Route::post('/asistencias/cerrar', [AsistenciaController::class, 'cerrarMes'])->name('asistencias.cerrar');
    
Route::get('/asistencias/historico', [AsistenciaController::class, 'historico'])
 ->name('asistencias.historico');
});