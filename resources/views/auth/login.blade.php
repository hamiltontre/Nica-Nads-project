@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="atleta-card" style="max-width: 400px;">
        <div class="card-body p-4">
            <!-- Título grande centrado -->
            <div class="text-center mb-4">
                <h2 class="mb-0" style="font-size: 2.5rem; color: #3498db; font-weight: 600;">NICA NADADORES</h2>
                <p class="text-muted mt-2">Sistema de Gestión de Asistencias</p>
            </div>

            <!-- Formulario de login -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Campo Email -->
                <div class="mb-3">
                    <label for="email" class="form-label text-white">Email</label>
                    <input type="email" class="form-control bg-dark text-white border-dark" 
                           id="email" name="email" required placeholder="tu@email.com">
                </div>
                
                <!-- Campo Contraseña -->
                <div class="mb-4">
                    <label for="password" class="form-label text-white">Contraseña</label>
                    <input type="password" class="form-control bg-dark text-white border-dark" 
                           id="password" name="password" required placeholder="••••••••">
                </div>
                
                <!-- Botón de Ingreso -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2" style="background: #3498db; border: none;">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Ingresar
                    </button>
                </div>
                
                <!-- Enlace de recordatorio -->
                @if (Route::has('password.request'))
                    <div class="text-center mt-3">
                        <a href="{{ route('password.request') }}" class="text-muted small" 
                           style="color: #bdc3c7 !important;">¿Olvidaste tu contraseña?</a>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection