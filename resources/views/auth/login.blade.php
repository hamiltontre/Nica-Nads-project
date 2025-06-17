@extends('layouts.auth') 

@section('content')
<div class="mb-4">
    <h5 class="text-center">Iniciar Sesión</h5>
</div>
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-box-arrow-in-right"></i> Ingresar
        </button>
    </div>
</form>
@endsection