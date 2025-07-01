@extends('layouts.modern')

@section('title', 'Detalles de Factura')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg factura-card"> <!-- Agregada clase factura-card -->
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-receipt"></i> Detalles de Factura
            </h5>
        </div>
        
        <div class="card-body bg-white"> <!-- Fondo blanco sólido -->
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2 text-black">Información Básica</h5> <!-- text-black -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="text-black"><strong>Número de Factura:</strong></p>
                                <p class="text-black fw-bold">{{ $factura->numero_factura }}</p> <!-- fw-bold -->
                            </div>
                            <div class="col-md-6">
                                <p class="text-black"><strong>Fecha:</strong></p>
                                <p class="text-black">{{ $factura->fecha->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-black"><strong>Lugar:</strong></p>
                                <p class="text-black">{{ $factura->lugar }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-black"><strong>Monto:</strong></p>
                                <p class="text-black fw-bold">{{ $factura->simbolo_moneda }} {{ number_format($factura->monto, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2 text-black">Descripción</h5>
                        <p class="mt-3 text-black">{{ $factura->descripcion ?? 'Sin descripción' }}</p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2 text-black">Imagen de la Factura</h5>
                    <div class="mt-3 text-center">
                        <img src="{{ asset('storage/'.$factura->imagen_path) }}" 
                             alt="Factura {{ $factura->numero_factura }}" 
                             class="img-fluid rounded shadow" style="max-height: 400px;">
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="{{ route('facturas.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
            </div>
        </div>
    </div>
</div>
@endsection