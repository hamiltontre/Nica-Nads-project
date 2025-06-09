<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AtletaController;

// Ruta única de autenticación
Route::match(['get', 'post'], '/login', [AuthController::class, 'handleAuth'])->name('login');

// Grupo de rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard principal (redirige a atletas por ahora)
    Route::get('/', function () {
        return redirect()->route('atletas.index');
    });
    
    // Gestión de atletas
    Route::resource('atletas', AtletaController::class);
    
    // Grupos de entrenamiento
    Route::prefix('grupos')->group(function () {
        Route::view('/federados', 'grupos.federados')->name('grupos.federados');
        Route::view('/novatos', 'grupos.novatos')->name('grupos.novatos');
        Route::view('/juniors', 'grupos.juniors')->name('grupos.juniors');
        Route::view('/principiantes', 'grupos.principiantes')->name('grupos.principiantes');
    });

// Rutas para las nuevas secciones
    Route::get('/facturas', function () {
        return view('facturas.index');
    })->name('facturas.index');

    Route::get('/asistencias', function () {
        return view('asistencias.index');
    })->name('asistencias.index');
    
});

 