@extends('layouts.modern')

@section('title', 'Gestión de Facturas')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-receipt"></i> Registro de Facturas
                </h5>
                <a href="{{ route('facturas.create') }}" class="btn btn-light">
                    <i class="bi bi-plus-circle"></i> Nueva Factura
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" action="{{ route('facturas.index') }}">
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label for="simbolo_moneda" class="form-label">Moneda</label>
                                        <select class="form-select" id="simbolo_moneda" name="simbolo_moneda">
                                            <option value="">Todas</option>
                                            <option value="C$" {{ request('simbolo_moneda') == 'C$' ? 'selected' : '' }}>Córdobas (C$)</option>
                                            <option value="$" {{ request('simbolo_moneda') == '$' ? 'selected' : '' }}>Dólares ($)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="monto_min" class="form-label">Monto Mínimo</label>
                                        <input type="number" step="0.01" class="form-control" id="monto_min" 
                                               name="monto_min" value="{{ request('monto_min') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="monto_max" class="form-label">Monto Máximo</label>
                                        <input type="number" step="0.01" class="form-control" id="monto_max" 
                                               name="monto_max" value="{{ request('monto_max') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="fecha_inicio" class="form-label">Desde</label>
                                        <input type="date" class="form-control" id="fecha_inicio" 
                                               name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="fecha_fin" class="form-label">Hasta</label>
                                        <input type="date" class="form-control" id="fecha_fin" 
                                               name="fecha_fin" value="{{ request('fecha_fin') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="lugar" class="form-label">Lugar</label>
                                        <input type="text" class="form-control" id="lugar" 
                                               name="lugar" value="{{ request('lugar') }}">
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="bi bi-funnel"></i> Filtrar
                                        </button>
                                        <a href="{{ route('facturas.index') }}" class="btn btn-secondary">
                                            <i class="bi bi-arrow-counterclockwise"></i> Limpiar
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Listado de facturas -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Factura</th>
                            <th>Lugar</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($facturas as $factura)
                        <tr>
                            <td>{{ $factura->numero_factura }}</td>
                            <td>{{ $factura->lugar }}</td>
                            <td>{{ $factura->simbolo_moneda }} {{ number_format($factura->monto, 2) }}</td>
                            <td>{{ $factura->fecha->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('facturas.show', $factura->id) }}" 
                                   class="btn btn-sm btn-info" title="Ver detalles">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('facturas.edit', $factura->id) }}" 
                                   class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('facturas.destroy', $factura->id) }}" 
                                      method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            title="Eliminar" onclick="return confirm('¿Eliminar esta factura?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay facturas registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $facturas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection