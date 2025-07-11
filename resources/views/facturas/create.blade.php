@extends('layouts.modern')

@section('title', 'Nueva Factura')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-plus-circle"></i> Nueva Factura
            </h5>
        </div>
        
        <div class="card-body">
            <!-- Formulario para crear nueva factura -->
            <form action="{{ route('facturas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row g-3">
                    <!-- Campo para número de factura -->
                    <div class="col-md-6">
                        <label for="numero_factura" class="form-label">Número de Factura</label>
                        <input type="text" class="form-control @error('numero_factura') is-invalid @enderror" 
                               id="numero_factura" name="numero_factura" value="{{ old('numero_factura') }}" required>
                        @error('numero_factura')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Campo para monto con selector de moneda -->
                    <div class="col-md-6">
                        <label for="monto" class="form-label">Monto</label>
                        <div class="input-group">
                            <select class="form-select @error('simbolo_moneda') is-invalid @enderror" 
                                   name="simbolo_moneda" style="max-width: 80px;" required>
                                <option value="C$" {{ old('simbolo_moneda') == 'C$' ? 'selected' : 'selected' }}>C$</option>
                                <option value="$" {{ old('simbolo_moneda') == '$' ? 'selected' : '' }}>$</option>
                            </select>
                            <input type="number" step="0.01" class="form-control @error('monto') is-invalid @enderror" 
                                   id="monto" name="monto" value="{{ old('monto') }}" required>
                            @error('monto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @error('simbolo_moneda')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Campo para lugar -->
                    <div class="col-md-6">
                        <label for="lugar" class="form-label">Lugar</label>
                        <input type="text" class="form-control @error('lugar') is-invalid @enderror" 
                               id="lugar" name="lugar" value="{{ old('lugar') }}" required>
                        @error('lugar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Campo para fecha -->
                    <div class="col-md-6">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control @error('fecha') is-invalid @enderror" 
                               id="fecha" name="fecha" value="{{ old('fecha') }}" required>
                        @error('fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Campo para descripción -->
                    <div class="col-md-12">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Campo para imagen de factura -->
                    <div class="col-md-12">
                        <label for="imagen" class="form-label">Imagen de la Factura</label>
                        <input type="file" class="form-control @error('imagen') is-invalid @enderror" 
                               id="imagen" name="imagen" required>
                        @error('imagen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="col-md-12 mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-save"></i> Guardar Factura
                        </button>
                        <a href="{{ route('facturas.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection