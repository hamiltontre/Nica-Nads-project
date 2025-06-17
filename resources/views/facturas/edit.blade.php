@extends('layouts.modern')

@section('title', 'Editar Factura')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-pencil"></i> Editar Factura
            </h5>
        </div>
        
        <div class="card-body">
            <form action="{{ route('facturas.update', $factura->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="numero_factura" class="form-label">Número de Factura</label>
                        <input type="text" class="form-control @error('numero_factura') is-invalid @enderror" 
                               id="numero_factura" name="numero_factura" value="{{ old('numero_factura', $factura->numero_factura) }}" required>
                        @error('numero_factura')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="monto" class="form-label">Monto</label>
                        <div class="input-group">
                            <select class="form-select @error('simbolo_moneda') is-invalid @enderror" 
                                   name="simbolo_moneda" style="max-width: 80px;" required>
                                <option value="C$" {{ old('simbolo_moneda', $factura->simbolo_moneda) == 'C$' ? 'selected' : '' }}>C$</option>
                                <option value="$" {{ old('simbolo_moneda', $factura->simbolo_moneda) == '$' ? 'selected' : '' }}>$</option>
                            </select>
                            <input type="number" step="0.01" class="form-control @error('monto') is-invalid @enderror" 
                                   id="monto" name="monto" value="{{ old('monto', $factura->monto) }}" required>
                            @error('monto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @error('simbolo_moneda')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="lugar" class="form-label">Lugar</label>
                        <input type="text" class="form-control @error('lugar') is-invalid @enderror" 
                               id="lugar" name="lugar" value="{{ old('lugar', $factura->lugar) }}" required>
                        @error('lugar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control @error('fecha') is-invalid @enderror" 
                               id="fecha" name="fecha" value="{{ old('fecha', $factura->fecha->format('Y-m-d')) }}" required>
                        @error('fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-12">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $factura->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-12">
                        <label for="imagen" class="form-label">Nueva Imagen (Opcional)</label>
                        <input type="file" class="form-control @error('imagen') is-invalid @enderror" 
                               id="imagen" name="imagen">
                        @error('imagen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Dejar en blanco para mantener la imagen actual</small>
                    </div>
                    
                    <div class="col-md-12 mt-3">
                        <div class="card">
                            <div class="card-body">
                                <h6>Imagen Actual</h6>
                                <img src="{{ asset('storage/'.$factura->imagen_path) }}" 
                                     alt="Factura actual" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12 mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-save"></i> Actualizar Factura
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